import pandas as pd
import os
from unidecode import unidecode

BASE = os.path.dirname(os.path.abspath(__file__))
IN_FILE = os.path.join(BASE, "dataset.pkl")
OUT_FILE = os.path.join(BASE, "dataset_clean.pkl")
CURRENT_YEAR = 2026

#ETIQUETAS OFICIALES DE TMDB PROHIBIDAS
FORBIDDEN_KEYWORDS = {
    "erotica", "softcore", "pornography", "sex", "pink eiga", 
    "bdsm", "nudity", "sexual", "erotic movie", "adult", "incest",
    "rape", "fetish", "prostitute", "strip club"
}

def normalize(text):
    return unidecode(str(text)).lower()

def is_safe(row):
    if row.get("adult") is True:
        return False
        
    #Verificar si tiene etiquetas prohibidas
    movie_keywords = set(row.get("keywords", []))
    if movie_keywords & FORBIDDEN_KEYWORDS:
        return False
        
    return True

def is_valid(row):
    try:
        overview = row.get("overview")
        genres = row.get("genres")
        votes = row.get("votes", 0)
        rating = row.get("rating", 0)

        if not isinstance(genres, list) or not overview or len(genres) == 0:
            return False

        #UMBRAL ESTRICTO: La basura no consigue 150 votos reales.
        if votes < 150: 
            return False

        if rating < 5:
            return False

        return True
    except:
        return False

def is_recent(row):
    try:
        year = row.get("year")
        if not year: return False
        return (CURRENT_YEAR - int(year)) <= 35
    except: return False

if __name__ == "__main__":
    df = pd.read_pickle(IN_FILE)
    print("Dataset original:", len(df))

    df["display_title"] = df.apply(lambda r: normalize(r.get("display_title") or r.get("title") or r.get("original_title") or ""), axis=1)
    df = df.drop_duplicates(subset=["tmdb_id"])

    #Aplicar validación de votos y calidad
    mask_valid = df.apply(lambda row: bool(is_valid(row)), axis=1)
    df = df.loc[mask_valid]
    print("Después de validar votos y datos:", len(df))

    #Aplicar nuevo filtro de Metadatos y Keywords
    mask_safe = df.apply(lambda row: is_safe(row), axis=1)
    df = df.loc[mask_safe]
    print("Después de filtro estricto anti-adultos:", len(df))

    mask_recent = df.apply(lambda row: bool(is_recent(row)), axis=1)
    df = df.loc[mask_recent]

    df = df.reset_index(drop=True)
    print("Dataset FINAL limpio:", len(df))
    df.to_pickle(OUT_FILE)
    print("dataset_clean.pkl guardado con éxito")