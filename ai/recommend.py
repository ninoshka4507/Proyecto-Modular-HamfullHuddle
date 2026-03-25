import sys
import joblib
import numpy as np
import os
import re
from unidecode import unidecode

from user_profile import get_user_profile

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

try:
    tmdb_id = int(sys.argv[1])
    user_id = int(sys.argv[2])

    df = joblib.load(os.path.join(BASE_DIR, "dataset_clean.pkl"))
    embeddings = joblib.load(os.path.join(BASE_DIR, "embeddings.pkl"))
    model = joblib.load(os.path.join(BASE_DIR, "model.pkl"))

    mlb_genres, top_actors, top_dirs, scaler = joblib.load(
        os.path.join(BASE_DIR, "model_meta.pkl")
    )

    profile = get_user_profile(user_id)

    viewed_set = set((int(x["tmdb_id"]), x["type"]) for x in profile.get("viewed", []))
    liked_set = set((int(x["tmdb_id"]), x["type"]) for x in profile.get("liked", []))
    neutral_set = set((int(x["tmdb_id"]), x["type"]) for x in profile.get("neutral", []))
    disliked_set = set((int(x["tmdb_id"]), x["type"]) for x in profile.get("disliked", []))

    #LISTA NEGRA
    BAD_WORDS = [
        "bitch", "bitches", "sex", "porn", "xxx", "erotic", "nudity",
        "hentai", "bdsm", "escort", "orgy", "lust", "seduction", "playboy",
        "adult", "cum", "fart", "ass", "whore", "slut", "cock", "dick",
        "pussy", "boobs", "tits", "incest", "rape", "stepmom", "stepdad", "sensual"
    ]
    
    #Compilamos la expresión regular para buscar palabras enteras
    BAD_WORDS_PATTERN = re.compile(r'\b(' + '|'.join(BAD_WORDS) + r')\b', flags=re.IGNORECASE)

    def clean_text_for_match(text):
        text = unidecode(str(text)).lower()
        return re.sub(r'[^\w\s]', '', text)

    def is_safe(row):
        title = unidecode(str(row.get("display_title", ""))).lower()
        overview = unidecode(str(row.get("overview", ""))).lower()
        full_text = f"{title} {overview}"
        
        #Búsqueda estricta con Regex
        if BAD_WORDS_PATTERN.search(full_text): return False
        if "ghost story" in title: return False
        return True

    matches = df.index[df["tmdb_id"] == tmdb_id]
    if len(matches) == 0:
        print("fallback")
        sys.exit(0)

    input_idx = matches[0]
    input_row = df.iloc[input_idx]
    input_embedding = embeddings[input_idx]
    input_type = input_row.get("type")

    liked_embeddings = []

    for liked_id, liked_type in liked_set:
        match = df.index[
            (df["tmdb_id"] == liked_id) & (df["type"] == liked_type)
        ]
        if len(match) > 0:
            liked_embeddings.append(embeddings[match[0]])

    input_genres = set(input_row.get("genres",[]))
    input_year = input_row.get("year")
    input_language = input_row.get("original_language", "")

    input_title_clean = clean_text_for_match(input_row.get("display_title", ""))
    input_words = set(input_title_clean.split())
    stopwords = {"the", "a", "an", "of", "and", "in", "to", "movie", "film", "part", "vol", "chapter"}
    meaningful_input = input_words - stopwords

    similarities = embeddings @ input_embedding
    neighbors = np.argsort(-similarities)[:2000]

    valid = []

    for idx in neighbors:
        if idx == input_idx: continue

        row = df.iloc[idx]
        tmdb_id_row = int(row["tmdb_id"])
        row_type = row.get("type")

        if row_type != input_type: continue

        key = (tmdb_id_row, row_type)
        
        threshold = 0.45 if liked_embeddings else 0.50
        if similarities[idx] < threshold: continue
        if not is_safe(row): continue
        if key in viewed_set: continue
        if key in disliked_set: continue
        if row.get("votes",0) < 150: continue
        if not set(row.get("genres",[])) & input_genres: continue
        if input_year and row.get("year") and abs(row["year"] - input_year) > 15: continue
        if row.get("rating", 0) < 5: continue

        valid.append(idx)

    if len(valid) == 0:
        print("fallback")
        sys.exit(0)

    def build_features(row):
        genres_vec = mlb_genres.transform([row.get("genres", [])])[0]

        actor_vec = np.zeros(len(top_actors))
        for a in row.get("cast", [])[:5]:
            if a in top_actors:
                actor_vec[top_actors.index(a)] = 1

        dir_vec = np.zeros(len(top_dirs))
        if row.get("director") in top_dirs:
            dir_vec[top_dirs.index(row["director"])] = 1

        numeric = np.array([
            row.get("year") or 2000,
            row.get("rating") or 0,
            np.log1p(row.get("votes") or 0),
            row.get("popularity") or 0
        ]).reshape(1, -1)

        numeric_scaled = scaler.transform(numeric)[0]

        return np.hstack([genres_vec, actor_vec, dir_vec, numeric_scaled])

    X_features = np.array([build_features(df.iloc[i]) for i in valid])
    scores = model.predict_proba(X_features)[:, 1]

    results = []

    for i, idx in enumerate(valid):
        row = df.iloc[idx]
        title = row.get("display_title") or row.get("title") or unidecode(str(row.get("original_title","")))
        
        result_title_clean = clean_text_for_match(title)
        result_words = set(result_title_clean.split())
        meaningful_result = result_words - stopwords
        overlap = len(meaningful_input & meaningful_result)
        
        franchise_penalty = 0.0
        if overlap > 0 and (len(meaningful_input) == 1 or overlap >= 2):
            franchise_penalty = 2.0  

        tmdb_id_row = int(row["tmdb_id"])
        row_type = row.get("type")

        key = (tmdb_id_row, row_type)

        semantic_score = similarities[idx]

        liked_score = 0

        if liked_embeddings:
            sims = [embeddings[idx] @ emb for emb in liked_embeddings]
            liked_score = sum(sims) / len(sims)
            semantic_score = (semantic_score * 0.6) + (liked_score * 0.4)

        rf_score = scores[i]
        
        personalization_boost = 0.0
        row_genres = set(row.get("genres", []))
        
        if input_language and input_language == row.get("original_language"):
            if input_language in ['ja', 'ko', 'zh']:
                personalization_boost += 0.20 
            else:
                personalization_boost += 0.05

        shared_genres = len(input_genres & row_genres)
        if shared_genres >= 2:
            personalization_boost += (shared_genres * 0.05) 
        elif shared_genres == 1 and "Reality" in input_genres:
            personalization_boost += 0.15 
        elif shared_genres == 1 and "Animation" in input_genres:
            personalization_boost += 0.10 

        # --- PERFIL DE USUARIO EQUILIBRADO ---
        if set(profile.get("preferred_genres", [])) & row_genres: personalization_boost += 0.10
        if row.get("original_language") and row.get("original_language") in profile.get("preferred_languages", []): personalization_boost += 0.15
        if row.get("director") and row.get("director") in profile.get("preferred_directors", []): personalization_boost += 0.10
        if set(profile.get("preferred_actors", [])) & set(row.get("cast", [])[:5]): personalization_boost += 0.10

        if key in liked_set:
            personalization_boost += 0.4
        elif key in neutral_set:
            personalization_boost += 0.05

        quality_boost = (row.get("rating", 0) / 10) * 0.2
        #Fórmula ponderada: 70% IA, 15% Calidad, 15% Usuario
        final_score = (
            (semantic_score * 0.55) +
            (rf_score * 0.15) +
            quality_boost +
            personalization_boost -
            franchise_penalty
        )

        results.append({
            "tmdb_id": int(row["tmdb_id"]),
            "title": title,
            "year": row.get("year"),
            "rating": row.get("rating"),
            "score": final_score
        })

    results = sorted(results, key=lambda x: x["score"], reverse=True)

    seen_ids = set()
    final_results = []

    for r in results:
        key = r["tmdb_id"]

        if key in seen_ids:
            continue

        seen_ids.add(key)
        final_results.append(r)

        if len(final_results) >= 40:
            break

    for r in final_results:
        print(f"{r['tmdb_id']}|{r['title']}|{r['year']}|{r['rating']}")

except Exception as e:
    print("fallback")
    sys.exit(0)