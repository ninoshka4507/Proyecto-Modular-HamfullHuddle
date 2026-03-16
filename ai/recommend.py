import sys
import joblib
import numpy as np
import os

from semantic_recommender import semantic_search
from user_embeddings import get_user_embedding

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

tmdb_id = int(sys.argv[1])
user_id = int(sys.argv[2])

df = joblib.load(os.path.join(BASE_DIR, "dataset.pkl"))
embeddings = joblib.load(os.path.join(BASE_DIR, "embeddings.pkl"))
model = joblib.load(os.path.join(BASE_DIR, "model.pkl"))

scores, neighbors = semantic_search(tmdb_id, 200)

user_vector = get_user_embedding(user_id)

user_similarity = {}

if user_vector is not None:

    sims = embeddings @ user_vector

    for i, s in enumerate(sims):
        user_similarity[i] = s

X = df[["rating","popularity","votes"]]
model_scores = model.predict_proba(X)[:,1]

final_scores = {}

for i, idx in enumerate(neighbors):

    semantic = scores[i]
    ml = model_scores[idx]
    user = user_similarity.get(idx,0)

    score = semantic*0.5 + ml*0.2 + user*0.3

    final_scores[idx] = score

sorted_movies = sorted(final_scores.items(), key=lambda x: x[1], reverse=True)

count = 0

for idx, score in sorted_movies:

    print(df.iloc[idx]["tmdb_id"], score)

    count += 1

    if count >= 6:
        break