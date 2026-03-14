import requests
import pandas as pd
from tqdm import tqdm

API_KEY = "8f47fb25b9aca0ce108e6b851326dc39"

BASE_URL = "https://api.themoviedb.org/3"

movies = []

TOTAL_PAGES = 250   #250 páginas ≈ 5000 películas

for page in tqdm(range(1, TOTAL_PAGES + 1)):

    url = f"{BASE_URL}/discover/movie"

    params = {
        "api_key": API_KEY,
        "language": "en-US",
        "sort_by": "popularity.desc",
        "page": page
    }

    response = requests.get(url, params=params)
    data = response.json()

    for movie in data["results"]:

        movies.append({
            "tmdb_id": movie["id"],
            "title": movie["title"],
            "genre_ids": movie["genre_ids"],
            "rating": movie["vote_average"],
            "popularity": movie["popularity"],
            "votes": movie["vote_count"],
            "year": movie["release_date"][:4] if movie["release_date"] else None
        })

df = pd.DataFrame(movies)

df.to_csv("ai/dataset_movies.csv", index=False)

print("Dataset creado con", len(df), "películas")