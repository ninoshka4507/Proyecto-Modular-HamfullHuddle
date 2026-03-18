import requests
import pandas as pd
from tqdm import tqdm
import os
from unidecode import unidecode
from concurrent.futures import ThreadPoolExecutor, as_completed
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry

API_KEY = "8f47fb25b9aca0ce108e6b851326dc39"
BASE_URL = "https://api.themoviedb.org/3"
OUT_CSV = "ai/dataset_movies.csv"
TOTAL_PAGES = 500
MAX_WORKERS = 12 

def create_session():
    session = requests.Session()
    retries = Retry(total=5, backoff_factor=0.3, status_forcelist=[500, 502, 503, 504])
    adapter = HTTPAdapter(max_retries=retries)
    session.mount("https://", adapter)
    return session

session = create_session()

def contains_cjk(text):
    if not text: return False
    for ch in text:
        code = ord(ch)
        if (0x4E00 <= code <= 0x9FFF) or (0x3040 <= code <= 0x30FF) or (0xAC00 <= code <= 0xD7AF):
            return True
    return False

def clean_title(title, original):
    if title and not contains_cjk(title): return title
    if original and not contains_cjk(original): return original
    return unidecode(original or title or "")

def get_details(tmdb_id, content_type):
    try:
        url = f"{BASE_URL}/{content_type}/{tmdb_id}"
        params = {"api_key": API_KEY, "language": "en-US", "append_to_response": "credits,keywords"}
        r = session.get(url, params=params, timeout=10)
        data = r.json()

        genres = [g["name"] for g in data.get("genres", [])]
        cast = [c["name"] for c in data.get("credits", {}).get("cast", [])[:5]]
        
        director = ""
        for crew in data.get("credits", {}).get("crew", []):
            if crew.get("job") == "Director":
                director = crew.get("name")
                break

        overview = data.get("overview", "")
        
        #EXTRAER KEYWORDS DE COMUNIDAD
        kw_data = data.get("keywords", {})
        kw_list = kw_data.get("keywords", []) or kw_data.get("results", [])
        keywords = [k["name"].lower() for k in kw_list]

        return genres, cast, director, overview, keywords
    except:
        return [], [], "", "", []

def process_item(m, endpoint):
    #BLOQUEO ABSOLUTO DE CAPA 1
    if m.get("adult") is True:
        return None

    tmdb_id = m["id"]
    genres, cast, director, overview, keywords = get_details(tmdb_id, endpoint)

    title = m.get("title") or m.get("name")
    original = m.get("original_title") or m.get("original_name")
    display_title = clean_title(title, original)

    year = ""
    if m.get("release_date"): year = m["release_date"][:4]
    if m.get("first_air_date"): year = m["first_air_date"][:4]

    return {
        "tmdb_id": tmdb_id,
        "title": display_title,
        "display_title": display_title,
        "genres": genres,
        "cast": cast,
        "director": director,
        "overview": overview,
        "keywords": keywords, 
        "adult": m.get("adult", False), 
        "rating": m.get("vote_average", 0.0),
        "popularity": m.get("popularity", 0.0),
        "votes": m.get("vote_count", 0),
        "year": int(year) if year.isdigit() else None,
        "type": endpoint,
        "original_language": m.get("original_language"),
    }

def fetch(endpoint):
    results = []
    for page in tqdm(range(1, TOTAL_PAGES + 1), desc=f"{endpoint}"):
        url = f"{BASE_URL}/discover/{endpoint}"
        params = {"api_key": API_KEY, "language": "en-US", "sort_by": "popularity.desc", "page": page}
        try:
            r = session.get(url, params=params, timeout=10)
            data = r.json()
        except: continue
        if "results" not in data: continue

        with ThreadPoolExecutor(max_workers=MAX_WORKERS) as executor:
            futures = [executor.submit(process_item, m, endpoint) for m in data["results"]]
            for future in as_completed(futures):
                try:
                    result = future.result()
                    if result: results.append(result)
                except: continue
    return results

if __name__ == "__main__":
    print("Descargando películas...")
    movies = fetch("movie")
    print("Descargando series...")
    tv = fetch("tv")

    all_data = movies + tv
    df = pd.DataFrame(all_data)
    df.drop_duplicates(subset=["tmdb_id"], inplace=True)
    os.makedirs("ai", exist_ok=True)
    df.to_pickle("ai/dataset.pkl")
    df.to_csv(OUT_CSV, index=False)
    print("Dataset creado:", len(df))