import psycopg2
import pandas as pd

def get_user_profile(user_id):

    conn = psycopg2.connect(
        dbname="hamfuhuddle_db",
        user="postgres",
        password="root",
        host="localhost"
    )

    q = f"""
    SELECT tmdb_id FROM vistas WHERE usuario_id={user_id}
    UNION
    SELECT tmdb_id FROM busquedas WHERE usuario_id={user_id}
    """

    df = pd.read_sql(q,conn)

    conn.close()

    return df["tmdb_id"].tolist()