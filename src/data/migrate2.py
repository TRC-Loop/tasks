import json
import sqlite3

DB_NAME = "tasks_projects.db"
JSON_FILE = "todos_copy.json"

def create_tables(cursor):
    cursor.executescript('''
        CREATE TABLE IF NOT EXISTS projects (
            uuid TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            created TEXT NOT NULL,
            user_id INTEGER NOT NULL
        );
        
        CREATE TABLE IF NOT EXISTS sections (
            uuid TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            created TEXT NOT NULL,
            project_uuid TEXT NOT NULL,
            FOREIGN KEY (project_uuid) REFERENCES projects(uuid)
        );
        
        CREATE TABLE IF NOT EXISTS todos (
            uuid TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            created TEXT NOT NULL,
            completed BOOLEAN DEFAULT FALSE,
            completed_when TEXT,
            deleted BOOLEAN DEFAULT FALSE,
            section_uuid TEXT NOT NULL,
            FOREIGN KEY (section_uuid) REFERENCES sections(uuid)
        );
    ''')

def insert_data(cursor, projects):
    for project in projects:
        cursor.execute(
            """
            INSERT INTO projects (uuid, name, created, user_id)
            VALUES (?, ?, ?, ?)
            """,
            (project["uuid"], project["name"], project["created"], project.get("user_id", 0))
        )

        for section in project.get("sections", []):
            cursor.execute(
                """
                INSERT INTO sections (uuid, name, created, project_uuid)
                VALUES (?, ?, ?, ?)
                """,
                (section["uuid"], section["name"], section["created"], project["uuid"])
            )

            for todo in section.get("todos", []):
                cursor.execute(
                    """
                    INSERT INTO todos (uuid, name, created, completed, completed_when, deleted, section_uuid)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                    """,
                    (
                        todo["uuid"], todo["name"], todo["created"],
                        todo.get("completed", False), todo.get("completed_when", ""),
                        todo.get("deleted", False), section["uuid"]
                    )
                )

def migrate():
    with open(JSON_FILE, "r", encoding="utf-8") as f:
        projects = json.load(f)
    
    with sqlite3.connect(DB_NAME) as conn:
        cursor = conn.cursor()
        create_tables(cursor)
        insert_data(cursor, projects)
        conn.commit()

if __name__ == "__main__":
    migrate()
    print("Migration completed successfully.")