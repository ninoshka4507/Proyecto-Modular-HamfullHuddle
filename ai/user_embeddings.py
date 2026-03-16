import joblib
import numpy as np
import os
from user_profile import get_user_profile

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

df = joblib.load(os.path.join(BASE_DIR, "dataset.pkl"))
embeddings = joblib.load(os.path.join(BASE_DIR, "embeddings.pkl"))


def get_user_embedding(user_id):

    history = get_user_profile(user_id)

    vectors = []

    for h in history:

        idx = df.index[df["tmdb_id"] == h]

        if len(idx) > 0:
            vectors.append(embeddings[idx[0]])

    if len(vectors) == 0:
        return None

    user_vector = np.mean(vectors, axis=0)

    return user_vector