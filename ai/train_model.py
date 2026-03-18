import joblib
import os
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import MultiLabelBinarizer, StandardScaler
from sklearn.metrics import classification_report
from collections import Counter

BASE = os.path.dirname(os.path.abspath(__file__))


# -------------------------
# ACTORES FRECUENTES
# -------------------------
def top_n_actors(df, n=150):  
    cnt = Counter()
    for cast in df["cast"].dropna():
        for a in cast[:5]:
            cnt[a] += 1
    return [a for a, _ in cnt.most_common(n)]


# -------------------------
# FEATURE ENGINEERING
# -------------------------
def featurize(df):

    #géneros
    mlb_genres = MultiLabelBinarizer()
    genres_mat = mlb_genres.fit_transform(
        df["genres"].apply(lambda x: x if isinstance(x, list) else [])
    )

    #actores
    top_actors = top_n_actors(df)
    actor_map = {a: i for i, a in enumerate(top_actors)}

    actor_features = np.zeros((len(df), len(top_actors)), dtype=np.float32)

    for i, cast in enumerate(df["cast"]):
        if isinstance(cast, list):
            for a in cast[:5]:
                if a in actor_map:
                    actor_features[i, actor_map[a]] = 1

    #director
    top_dirs = df["director"].value_counts().index.tolist()[:80]
    dir_map = {d: i for i, d in enumerate(top_dirs)}

    dir_features = np.zeros((len(df), len(top_dirs)), dtype=np.float32)

    for i, d in enumerate(df["director"]):
        if d in dir_map:
            dir_features[i, dir_map[d]] = 1

    #features numéricas
    numeric = np.vstack([
        df["year"].fillna(2000).astype(float),
        df["rating"].fillna(0.0),
        np.log1p(df["votes"].fillna(0)),
        df["popularity"].fillna(0.0)
    ]).T

    #NORMALIZACIÓN (MUY IMPORTANTE)
    scaler = StandardScaler()
    numeric_scaled = scaler.fit_transform(numeric)

    X = np.hstack([genres_mat, actor_features, dir_features, numeric_scaled])

    return X, mlb_genres, top_actors, top_dirs, scaler


# -------------------------
# MAIN
# -------------------------
if __name__ == "__main__":

    df = joblib.load(os.path.join(BASE, "dataset_clean.pkl"))

    #etiqueta
    y = (df["rating"] >= 7).astype(int).values

    print("Generando features...")
    X, mlb_genres, top_actors, top_dirs, scaler = featurize(df)

    print("Split train/test...")
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )

    print("Entrenando modelo...")
    model = RandomForestClassifier(
        n_estimators=150,
        max_depth=15,
        n_jobs=-1,
        random_state=42
    )

    model.fit(X_train, y_train)

    preds = model.predict(X_test)

    print("\nRESULTADOS:")
    print(classification_report(y_test, preds))

    joblib.dump(model, os.path.join(BASE, "model.pkl"))
    joblib.dump(
        (mlb_genres, top_actors, top_dirs, scaler),
        os.path.join(BASE, "model_meta.pkl")
    )

    print("Modelo optimizado guardado")