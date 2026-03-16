import requests
import pandas as pd
import time
from tqdm import tqdm

API_KEY = "8f47fb25b9aca0ce108e6b851326dc39"
BASE_URL = "https://api.themoviedb.org/3"

movies = []

TOTAL_PAGES = 500   #500 páginas ≈ 10k items por tipo


def get_details(tmdb_id, content_type):

    url = f"{BASE_URL}/{content_type}/{tmdb_id}"

    params = {
        "api_key": API_KEY,
        "language": "en-US",
        "append_to_response": "credits,keywords"
    }

    data = requests.get(url, params=params).json()

    genres = [g["name"] for g in data.get("genres", [])]

    cast = []
    director = ""

    if "credits" in data:

        cast = [c["name"] for c in data["credits"]["cast"][:5]]

        for crew in data["credits"]["crew"]:
            if crew["job"] == "Director":
                director = crew["name"]
                break

    keywords = []

    if "keywords" in data:

        if "keywords" in data["keywords"]:
            keywords = [k["name"] for k in data["keywords"]["keywords"]]

        if "results" in data["keywords"]:
            keywords = [k["name"] for k in data["keywords"]["results"]]

    overview = data.get("overview", "")

    return {

        "genres": " ".join(genres),
        "cast": " ".join(cast),
        "director": director,
        "keywords": " ".join(keywords),
        "overview": overview
    }


def fetch(endpoint):

    for page in tqdm(range(1, TOTAL_PAGES + 1)):

        url = f"{BASE_URL}/discover/{endpoint}"

        params = {
            "api_key": API_KEY,
            "language": "en-US",
            "sort_by": "popularity.desc",
            "page": page
        }

        data = requests.get(url, params=params).json()

        if "results" not in data:
            continue

        for m in data["results"]:

            tmdb_id = m["id"]

            details = get_details(tmdb_id, endpoint)

            title = m.get("title") or m.get("name")

            year = ""

            if m.get("release_date"):
                year = m["release_date"][:4]

            if m.get("first_air_date"):
                year = m["first_air_date"][:4]

            movies.append({

                "tmdb_id": tmdb_id,
                "title": title,
                "genres": details["genres"],
                "cast": details["cast"],
                "director": details["director"],
                "keywords": details["keywords"],
                "overview": details["overview"],
                "rating": m["vote_average"],
                "popularity": m["popularity"],
                "votes": m["vote_count"],
                "year": year,
                "type": endpoint

            })

            time.sleep(0.05)  #Evitar rate limit


print("Descargando películas...")
fetch("movie")

print("Descargando series...")
fetch("tv")

df = pd.DataFrame(movies)

df.drop_duplicates(subset=["tmdb_id"], inplace=True)

df.to_csv("ai/dataset_movies.csv", index=False)

print("Dataset creado:", len(df))