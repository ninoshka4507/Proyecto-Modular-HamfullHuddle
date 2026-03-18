import joblib
import numpy as np
import os
from sentence_transformers import SentenceTransformer
import pandas as pd
import sys

BASE = os.path.dirname(os.path.abspath(__file__))
MODEL_NAME = "all-MiniLM-L6-v2"

BATCH_SIZE = 128 


def clean_text(text):
    if not text:
        return ""
    return str(text).replace("\n", " ").strip()


def make_text(row):
    parts = []

    #ORDEN IMPORTA (más peso al inicio)
    if row.get("display_title"):
        parts.append(clean_text(row["display_title"])) 
    if row.get("genres"):
        parts.append(" ".join(row["genres"]) * 2)

    if row.get("cast"):
        parts.append(" ".join(row["cast"][:5]))

    if row.get("director"):
        parts.append(row["director"])

    if row.get("overview"):
        parts.append(clean_text(row["overview"]))

    return " . ".join(parts)


if __name__ == "__main__": 

    df = pd.read_pickle(os.path.join(BASE, "dataset_clean.pkl"))

    #Validación de seguridad: Si el dataset se vació, avisa de inmediato
    if len(df) == 0:
        print("ERROR: El dataset_clean.pkl está vacío.")
        print("Esto significa que los filtros en clean_dataset.py eliminaron TODO.")
        sys.exit(1)

    print(f"Generando textos para {len(df)} registros...")
    
    texts = [make_text(row) for _, row in df.iterrows()]

    print("Cargando modelo...")
    model = SentenceTransformer(MODEL_NAME)

    print("Generando embeddings...")
    embeddings = model.encode(
        texts,
        batch_size=BATCH_SIZE,
        show_progress_bar=True,
        convert_to_numpy=True,
        normalize_embeddings=True  
    )

    joblib.dump(embeddings, os.path.join(BASE, "embeddings.pkl"))

    print("Embeddings listos:", embeddings.shape)