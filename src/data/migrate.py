import json
import sqlite3

DB_FILE = "tasks_users.db"
JSON_FILE = "users_copy.json"

def get_db_connection():
    conn = sqlite3.connect(DB_FILE)
    conn.execute("""
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE,
            password_hash TEXT,
            email TEXT,
            permissions TEXT,
            picture TEXT
        )
    """)
    return conn

def insert_user(conn, user):
    try:
        conn.execute(
            "INSERT INTO users (id, name, password_hash, email, permissions, picture) VALUES (?, ?, ?, ?, ?, ?)",
            (user["id"], user["name"], user["password_hash"], user["email"], json.dumps(user["permissions"]), user["picture"])
        )
        conn.commit()
    except sqlite3.IntegrityError:
        print(f"User {user['name']} already exists, skipping...")

def main():
    with open(JSON_FILE, "r", encoding="utf-8") as file:
        users = json.load(file)
    
    conn = get_db_connection()
    for user in users:
        insert_user(conn, user)
    conn.close()
    print("Users imported successfully.")

if __name__ == "__main__":
    main()
