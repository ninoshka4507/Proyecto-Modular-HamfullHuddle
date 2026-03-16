import joblib
import faiss
import numpy as np
import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

df = joblib.load(os.path.join(BASE_DIR, "dataset.pkl"))
index = faiss.read_index(os.path.join(BASE_DIR, "faiss.index"))
embeddings = joblib.load(os.path.join(BASE_DIR, "embeddings.pkl"))


def semantic_search(tmdb_id, k=50):

    idx = df.index[df["tmdb_id"] == tmdb_id].tolist()

    if not idx:
        return None

    idx = idx[0]

    query = embeddings[idx].reshape(1, -1)

    scores, neighbors = index.search(query, k)

    return scores[0], neighbors[0]