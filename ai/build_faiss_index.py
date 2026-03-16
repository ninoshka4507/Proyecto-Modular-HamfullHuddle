import joblib
import faiss
import numpy as np

print("Cargando embeddings...")

embeddings = joblib.load("ai/embeddings.pkl")

dim = embeddings.shape[1]

print("Creando índice FAISS...")

index = faiss.IndexFlatIP(dim)

index.add(embeddings)

faiss.write_index(index, "ai/faiss.index")

print("Indice FAISS creado:", index.ntotal)