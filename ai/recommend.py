import sys
import joblib
import numpy as np
import os
import re
from unidecode import unidecode

sys.stdout.reconfigure(encoding='utf-8')

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

    viewed_set = set(profile.get("viewed_tmdb_ids", []))
    liked_set = set(profile.get("liked_tmdb_ids", []))
    neutral_set = set(profile.get("neutral_tmdb_ids", []))
    disliked_set = set(profile.get("disliked_tmdb_ids", []))

    def clean_text_for_match(text):
        text = unidecode(str(text)).lower()
        return re.sub(r'[^\w\s]', '', text)
    
    user_embeddings = []

    for liked_id, liked_type in liked_set:
        match = df.index[(df["tmdb_id"] == liked_id) & (df["type"].str.lower() == str(liked_type).lower())]
        if len(match) > 0:
            user_embeddings.append(embeddings[match[0]] * 2)

    for viewed_id, viewed_type in viewed_set:
        match = df.index[(df["tmdb_id"] == viewed_id) & (df["type"].str.lower() == str(viewed_type).lower())]
        if len(match) > 0:
            user_embeddings.append(embeddings[match[0]])

    for neutral_id, neutral_type in neutral_set:
        match = df.index[(df["tmdb_id"] == neutral_id) & (df["type"].str.lower() == str(neutral_type).lower())]
        if len(match) > 0:
            user_embeddings.append(embeddings[match[0]] * 0.5)

    user_embedding = None
    if user_embeddings:
        user_embedding = np.mean(user_embeddings, axis=0)

    matches = df.index[df["tmdb_id"] == tmdb_id]
    if len(matches) == 0:
        if user_embedding is not None:
            input_embedding = user_embedding
            input_genres = set(profile.get("preferred_genres", []))
            input_year = None
            input_language = ""
            input_type = None
        else:
            input_genres = set(profile.get("preferred_genres", []))
            if len(input_genres) > 0:
                input_embedding = np.zeros(embeddings.shape[1])
                input_year = None
                input_language = ""
                input_type = None
            else:
                print("fallback")
                sys.exit(0)

    else:
        input_idx = matches[0]
        input_row = df.iloc[input_idx]
        input_embedding = embeddings[input_idx]
        input_type = input_row.get("type")

        if len(matches) > 0:
            input_genres = set(input_row.get("genres", []))
            input_year = input_row.get("year")
            input_language = input_row.get("original_language", "")
        else:
            input_genres = set()
            input_year = None
            input_language = ""

    liked_embeddings = []

    for liked_id, liked_type in liked_set:
        match = df.index[
            (df["tmdb_id"] == liked_id) & (df["type"].str.lower() == str(liked_type).lower())
        ]
        if len(match) > 0:
            liked_embeddings.append(embeddings[match[0]])

    disliked_embeddings = []

    for disliked_id, disliked_type in disliked_set:
        match = df.index[(df["tmdb_id"] == disliked_id) & (df["type"].str.lower() == str(disliked_type).lower())]
        if len(match) > 0:
            disliked_embeddings.append(embeddings[match[0]])

    if len(matches) > 0:
        input_title_clean = clean_text_for_match(input_row.get("display_title", ""))
    else:
        input_title_clean = ""
    input_words = set(input_title_clean.split())
    stopwords = {"the", "a", "an", "of", "and", "in", "to", "movie", "film", "part", "vol", "chapter"}
    meaningful_input = input_words - stopwords

    similarities = embeddings @ input_embedding

    if len(matches) == 0 and user_embedding is None:
        mask = df["genres"].apply(lambda g: len(set(g if isinstance(g, list) else []) & input_genres) > 0)
        matching_indices = df.index[mask].tolist()
        
        if len(matching_indices) > 0:
            neighbors = df.loc[matching_indices].sort_values("votes", ascending=False).head(2000).index.tolist()
        else:
            neighbors = np.argsort(-similarities)[:2000]
            
        threshold = -1.0
    else:
        neighbors = np.argsort(-similarities)[:2000]
        threshold = np.percentile(similarities, 70)

    valid = []

    for idx in neighbors:
        if len(matches) > 0 and idx == input_idx: continue

        row = df.iloc[idx]
        tmdb_id_row = int(row["tmdb_id"])
        row_type = row.get("type")

        key = (tmdb_id_row, row_type)

        if len(matches) == 0 and user_embedding is None:
            preferred_langs = profile.get("preferred_languages", [])
            user_genres = profile.get("preferred_genres", [])
            row_lang = row.get("original_language", "unknown")
            
            g_val = row.get("genres", [])
            row_genres = set(g_val if isinstance(g_val, list) else [])

            if "Animation" in row_genres and "Anime" in user_genres and row_lang != "ja":
                continue
           
            is_asian_fan = any(d in user_genres for d in ["K-Drama", "C-Drama", "J-Drama"])
            if "Drama" in row_genres and is_asian_fan:
                if len(preferred_langs) > 0 and row_lang not in preferred_langs:
                    continue

        if similarities[idx] < threshold: continue

        if key in viewed_set or key in liked_set or key in neutral_set or key in disliked_set: continue

        if row.get("votes",0) < 50: continue

        shared = len(set(row.get("genres",[])) & input_genres)
        if shared == 0 and similarities[idx] < 0.6: continue

        if input_year and row.get("year") and abs(row["year"] - input_year) > 15: continue

        if row.get("rating", 0) < 5: continue

        valid.append(idx)

    if len(valid) == 0:
        print("fallback")
        sys.exit(0)

    def build_features(row):
        g = row.get("genres")
        genres_list = g if isinstance(g, list) else []
        genres_vec = mlb_genres.transform([genres_list])[0]

        actor_vec = np.zeros(len(top_actors))
        c = row.get("cast")
        cast_list = c if isinstance(c, list) else []
        for a in cast_list[:5]:
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

        if overlap >= 3: continue
        
        franchise_penalty = 0.0
        if overlap > 0 and (len(meaningful_input) == 1 or overlap >= 2):
            franchise_penalty = 0.3  

        tmdb_id_row = int(row["tmdb_id"])
        row_type = row.get("type")

        key = (tmdb_id_row, row_type)

        dislike_penalty = 0.0
        
        if disliked_embeddings:
            dislike_sims = [embeddings[idx] @ emb for emb in disliked_embeddings]
            max_dislike_sim = max(dislike_sims) 
            if max_dislike_sim > 0.60:
                continue 
            elif max_dislike_sim > 0.50:
                dislike_penalty = max_dislike_sim * 1.5

        semantic_score = similarities[idx]

        user_sim = 0
        liked_sim = 0

        if user_embedding is not None:
            user_sim = embeddings[idx] @ user_embedding

        if liked_embeddings:
            sims = [embeddings[idx] @ emb for emb in liked_embeddings]
            liked_sim = sum(sims) / len(sims)

        if len(matches) > 0:
            if user_embedding is not None:
                semantic_score = (
                    (semantic_score * 0.80) + 
                    (user_sim * 0.10) +       
                    (liked_sim * 0.10)        
                )
            else:
                semantic_score = (
                    (semantic_score * 0.85) + 
                    (liked_sim * 0.15)
                )

        else:
            if user_embedding is not None:
                semantic_score = (
                    (semantic_score * 0.50) + 
                    (user_sim * 0.30) + 
                    (liked_sim * 0.20)
                )
            else:
                semantic_score = (
                    (semantic_score * 0.70) + 
                    (liked_sim * 0.30)
                )

        rf_score = scores[i]

        personalization_boost = 0.0
        
        g = row.get("genres")
        row_genres = set(g if isinstance(g, list) else [])

        if input_language and input_language == row.get("original_language"):
            personalization_boost += 0.10 

        shared_genres = len(input_genres & row_genres)
        if shared_genres >= 2:
            personalization_boost += (shared_genres * 0.05)

        if row.get("original_language") in profile.get("preferred_languages", []): 
            if len(matches) > 0:
                personalization_boost += 0.05 
            else:
                personalization_boost += 0.15 

        user_preferred_genres = set(profile.get("preferred_genres", []))
        shared_preferred = len(user_preferred_genres & row_genres)

        if shared_preferred > 0:
            personalization_boost += min(0.60, shared_preferred * 0.20)
        elif len(user_preferred_genres) > 0:
            semantic_score *= 0.50
            
        if row.get("director") and row.get("director") in profile.get("preferred_directors", []): 
            personalization_boost += 0.10

        c = row.get("cast")
        cast_list = c if isinstance(c, list) else []
        if set(profile.get("preferred_actors", [])) & set(cast_list[:5]): 
            personalization_boost += 0.10

        quality_boost = (row.get("rating", 0) / 10) * 0.2

        final_score = (
            (semantic_score * 0.50) +
            (rf_score * 0.20) +
            (quality_boost * 0.15) +
            (personalization_boost * 0.15) -
            franchise_penalty -
            dislike_penalty   
        )

        results.append({
            "tmdb_id": int(row["tmdb_id"]),
            "title": title,
            "year": row.get("year"),
            "rating": row.get("rating"),
            "score": final_score,
            "type": row_type,
            "lang": row.get("original_language", "unknown")
        })

    results = sorted(results, key=lambda x: x["score"], reverse=True)

    seen_ids = set()
    final_results = []
    lang_counts = {}

    for r in results:
        key = r["tmdb_id"]

        if key in seen_ids:
            continue

        lang = r.get("lang", "unknown")
        if lang_counts.get(lang, 0) >= 80:
            continue

        lang_counts[lang] = lang_counts.get(lang, 0) + 1

        seen_ids.add(key)
        final_results.append(r)

        if len(final_results) >= 100:
            break

    for r in final_results:
        print(f"{r['tmdb_id']}|{r['title']}|{r['year']}|{r['rating']}|{r['score']}|{r['type']}")

except Exception as e:
    print("ERROR REAL:", e)
    import traceback
    traceback.print_exc()
    sys.exit(1)