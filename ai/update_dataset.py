import pandas as pd
import psycopg2
import json
import os
import re
from unidecode import unidecode

conn = psycopg2.connect("dbname=hamfuhuddle_db user=postgres password=root")
db_df = pd.read_sql("SELECT * FROM contenido", conn)
db_df["genres"] = db_df["generos"].apply(lambda x: json.loads(x) if x else [])
db_df["cast"] = db_df["cast_list"].apply(lambda x: json.loads(x) if x else [])
db_df["type"] = db_df["tipo"]
db_df["tmdb_id"] = db_df["tmdb_id"].astype(int)
db_df["rating"] = db_df["rating"].fillna(0)
db_df["votes"] = db_df["votes"].fillna(0)
db_df["year"] = db_df["year"].fillna(2000)
db_df["popularity"] = db_df["popularidad"].fillna(0)
db_df["original_language"] = db_df["language"].fillna("en")

base_file = "dataset.pkl" 
if os.path.exists(base_file):
    base_df = pd.read_pickle(base_file)
    df = pd.concat([base_df, db_df], ignore_index=True)
    df = df.drop_duplicates(subset=["tmdb_id"], keep="last")
else:
    df = db_df
    print("ADVERTENCIA: No se encontró dataset.pkl. Usando solo la BD local.")

ALLOWED_LANGS = {'en', 'es', 'ja', 'ko', 'zh', 'fr', 'it', 'de', 'pt'}
BAD_WORDS = [
    "porn", "xxx", "erotica", "hentai", "bdsm", "orgy", "kamasutra", 
    "nympho", "hot girls wanted", "erotic", "stepmom", "stepdad", "sensuelle",
    "bitch", "bitches", "sex", "nudity", "escort", "lust", "seduction", "playboy",
    "adult", "cum", "fart", "ass", "whore", "slut", "cock", "dick", "pussy", "boobs", 
    "tits", "incest", "rape", "sensual"
]
BAD_WORDS_PATTERN = re.compile(r'\b(' + '|'.join(BAD_WORDS) + r')\b', flags=re.IGNORECASE)

def is_safe_and_valid(row):
    if row.get("adult") is True: return False
    if row.get("rating", 0) < 5: return False
    if row.get("votes", 0) < 50: return False 
    
    lang = str(row.get("original_language", "")).lower()
    if lang not in ALLOWED_LANGS: return False
    title = unidecode(str(row.get("display_title") or row.get("title") or "")).lower()
    overview = unidecode(str(row.get("overview", ""))).lower()
    full_text = f"{title} {overview}"
    
    if BAD_WORDS_PATTERN.search(full_text): return False
    if "ghost story" in title: return False
    
    return True

print(f"Total antes de filtrar: {len(df)}")
mask = df.apply(is_safe_and_valid, axis=1)
df_clean = df[mask].copy()
df_clean = df_clean.reset_index(drop=True)


df_clean.to_pickle("dataset_clean.pkl")
print(f"Dataset FINAL listo para Machine Learning: {len(df_clean)} títulos.")