import pandas as pd
import numpy as np
import joblib
from sentence_transformers import SentenceTransformer
from tqdm import tqdm

print("Cargando dataset...")

df = pd.read_csv("ai/dataset_movies.csv")

# limpiar texto
df["overview"] = df["overview"].fillna("")
df["genres"] = df["genres"].fillna("")
df["keywords"] = df["keywords"].fillna("")
df["cast"] = df["cast"].fillna("")
df["title"] = df["title"].fillna("")

# texto combinado
texts = (
    df["title"] + " " +
    df["genres"] + " " +
    df["keywords"] + " " +
    df["cast"] + " " +
    df["overview"]
)

print("Cargando modelo de embeddings...")

model = SentenceTransformer("all-MiniLM-L6-v2")

print("Generando embeddings...")

embeddings = model.encode(
    texts.tolist(),
    batch_size=64,
    show_progress_bar=True,
    convert_to_numpy=True
)

# normalizar embeddings (mejora cosine similarity)
norms = np.linalg.norm(embeddings, axis=1, keepdims=True)
embeddings = embeddings / norms

print("Guardando archivos optimizados...")

joblib.dump(df, "ai/dataset.pkl")
joblib.dump(embeddings, "ai/embeddings.pkl")

print("Embeddings generados:", len(embeddings))