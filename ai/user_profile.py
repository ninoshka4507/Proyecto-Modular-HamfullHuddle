import os
import json
import psycopg2
from psycopg2.extras import RealDictCursor
import joblib

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

def get_db_conn():
    return psycopg2.connect(
        host=os.environ.get("DB_HOST", "127.0.0.1"),
        port=os.environ.get("DB_PORT", "5432"),
        dbname=os.environ.get("DB_DATABASE", "hamfuhuddle_db"),
        user=os.environ.get("DB_USERNAME", "postgres"),
        password=os.environ.get("DB_PASSWORD", "root"),
    )

def get_user_profile(user_id):
    """
    Perfil completo del usuario basado en:
    - géneros elegidos
    - historial (vistas)
    - calificaciones (me gustó / neutro / no me gustó)
    - actores y directores desde dataset
    """
    fallback = os.path.join(BASE_DIR, "user_profiles.json")

    try:
        conn = get_db_conn()
        cur = conn.cursor(cursor_factory=RealDictCursor)

        # -------------------------
        # 1. GÉNEROS FAVORITOS
        # -------------------------
        cur.execute("""
            SELECT g.nombre
            FROM usuario_generos ug
            JOIN generos g ON ug.genero_id = g.id
            WHERE ug.usuario_id = %s
        """, (user_id,))

        rows = cur.fetchall()
        preferred_genres = [r["nombre"] for r in rows] if rows else []

        preferred_languages = []
        
        # --- LÓGICA DE MICRO-GÉNEROS ---
        
        #Anime: Japonés + Animación
        if "Anime" in preferred_genres:
            if "ja" not in preferred_languages: preferred_languages.append("ja")
            if "Animation" not in preferred_genres: preferred_genres.append("Animation")

        #K-Drama: Coreano + Drama
        if "K-Drama" in preferred_genres:
            if "ko" not in preferred_languages: preferred_languages.append("ko")
            if "Drama" not in preferred_genres: preferred_genres.append("Drama")

        #C-Drama: Chino + Drama
        if "C-Drama" in preferred_genres:
            if "zh" not in preferred_languages: preferred_languages.append("zh")
            if "Drama" not in preferred_genres: preferred_genres.append("Drama")

        #J-Drama: Japonés (Live Action) + Drama
        if "J-Drama" in preferred_genres:
            if "ja" not in preferred_languages: preferred_languages.append("ja")
            if "Drama" not in preferred_genres: preferred_genres.append("Drama")

        # -------------------------
        # 2. HISTORIAL (VISTAS)
        # -------------------------
        cur.execute("""
            SELECT tmdb_id
            FROM vistas
            WHERE usuario_id = %s
            ORDER BY fecha_vista DESC
            LIMIT 200
        """, (user_id,))

        rows = cur.fetchall()
        viewed_ids = [r["tmdb_id"] for r in rows] if rows else []

        # -------------------------
        # 3. CALIFICACIONES (3 niveles)
        # -------------------------
        cur.execute("""
            SELECT tmdb_id, calificacion
            FROM calificaciones
            WHERE usuario_id = %s
        """, (user_id,))

        rows = cur.fetchall()

        liked_ids = []
        neutral_ids = []
        disliked_ids = []

        for r in rows:
            if r["calificacion"] == "me gustó":
                liked_ids.append(r["tmdb_id"])
            elif r["calificacion"] == "neutro":
                neutral_ids.append(r["tmdb_id"])
            elif r["calificacion"] == "no me gustó":
                disliked_ids.append(r["tmdb_id"])

        # -------------------------
        # 4. COMBINAR DATOS POSITIVOS
        # -------------------------
        positive_ids = list(set(liked_ids + viewed_ids))

        # -------------------------
        # 5. ACTORES Y DIRECTORES (desde dataset)
        # -------------------------
        preferred_actors = []
        preferred_directors = []

        if positive_ids:
            df = joblib.load(os.path.join(BASE_DIR, "dataset.pkl"))
            subset = df[df["tmdb_id"].isin(positive_ids)]

            for _, row in subset.iterrows():
                if isinstance(row.get("cast"), list):
                    preferred_actors += row["cast"][:5]
                if row.get("director"):
                    preferred_directors.append(row["director"])

        conn.close()

        return {
            "preferred_genres": preferred_genres,
            "preferred_languages": preferred_languages, 
            "liked_tmdb_ids": liked_ids,
            "neutral_tmdb_ids": neutral_ids,
            "disliked_tmdb_ids": disliked_ids,
            "preferred_actors": preferred_actors,
            "preferred_directors": preferred_directors
        }

    except Exception as e:
        print("Error user_profile:", e)
        #fallback JSON
        if os.path.exists(fallback):
            with open(fallback, "r", encoding="utf-8") as f:
                all_profiles = json.load(f)

            return all_profiles.get(str(user_id), {
                "preferred_genres": [],
                "preferred_languages": [],
                "liked_tmdb_ids": [],
                "neutral_tmdb_ids": [],
                "disliked_tmdb_ids": [],
                "preferred_actors": [],
                "preferred_directors": []
            })

        return {
            "preferred_genres": [],
            "preferred_languages": [],
            "liked_tmdb_ids": [],
            "neutral_tmdb_ids": [],
            "disliked_tmdb_ids": [],
            "preferred_actors": [],
            "preferred_directors": []
        }