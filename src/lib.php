<?php
require_once "config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_db_connection_projects() {
  global $PROJECTS_DB;
    $db = new SQLite3($PROJECTS_DB);
    $db->exec("CREATE TABLE IF NOT EXISTS projects (
        uuid TEXT PRIMARY KEY,
        name TEXT,
        created TEXT,
        user_id INTEGER
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS sections (
        uuid TEXT PRIMARY KEY,
        name TEXT,
        created TEXT,
        project_uuid TEXT,
        FOREIGN KEY (project_uuid) REFERENCES projects(uuid) ON DELETE CASCADE
    )");
    $db->exec("CREATE TABLE IF NOT EXISTS todos (
        uuid TEXT PRIMARY KEY,
        name TEXT,
        created TEXT,
        completed BOOLEAN,
        completed_when TEXT,
        deleted BOOLEAN,
        section_uuid TEXT,
        FOREIGN KEY (section_uuid) REFERENCES sections(uuid) ON DELETE CASCADE
    )");
    return $db;
}

function generate_uuid(): string {
    return bin2hex(random_bytes(24));
}

function create_project(string $name, int $user_id) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("INSERT INTO projects (uuid, name, created, user_id) VALUES (?, ?, ?, ?)");
    $uuid = generate_uuid();
    $stmt->bindValue(1, $uuid);
    $stmt->bindValue(2, $name);
    $stmt->bindValue(3, date("Y-m-d H:i:s"));
    $stmt->bindValue(4, $user_id);
    $stmt->execute();
}

function get_all_projects() {
    if (!isset($_SESSION["user_id"])) return [];

    $db = get_db_connection_projects();
    $stmt = $db->prepare("SELECT * FROM projects WHERE user_id = ?");
    $stmt->bindValue(1, $_SESSION["user_id"]);
    $result = $stmt->execute();
    $projects = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $projects[] = $row;
    }
    return $projects;
}

function get_all_sections(string $project_uuid) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("SELECT * FROM sections WHERE project_uuid = ?");
    $stmt->bindValue(1, $project_uuid);
    $result = $stmt->execute();
    $sections = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $sections[] = $row;
    }
    return $sections;
}

function get_all_todos(string $project_uuid, string $section_uuid) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("SELECT * FROM todos WHERE section_uuid = ?");
    $stmt->bindValue(1, $section_uuid);
    $result = $stmt->execute();
    $todos = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $todos[] = $row;
    }
    return $todos;
}

function delete_project(string $uuid, int $user_id) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("DELETE FROM projects WHERE uuid = ? AND user_id = ?");
    $stmt->bindValue(1, $uuid);
    $stmt->bindValue(2, $user_id);
    $stmt->execute();
}

function create_section(string $project_uuid, string $name) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("INSERT INTO sections (uuid, name, created, project_uuid) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, generate_uuid());
    $stmt->bindValue(2, $name);
    $stmt->bindValue(3, date("Y-m-d H:i:s"));
    $stmt->bindValue(4, $project_uuid);
    $stmt->execute();
}

function delete_section(string $project_uuid, string $section_uuid) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("DELETE FROM sections WHERE uuid = ?");
    $stmt->bindValue(1, $section_uuid);
    $stmt->execute();
}

function create_todo(string $project_uuid, string $section_uuid, string $name) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("INSERT INTO todos (uuid, name, created, completed, completed_when, deleted, section_uuid) VALUES (?, ?, ?, 0, '', 0, ?)");
    $stmt->bindValue(1, generate_uuid());
    $stmt->bindValue(2, $name);
    $stmt->bindValue(3, date("Y-m-d H:i:s"));
    $stmt->bindValue(4, $section_uuid);
    $stmt->execute();
}

function delete_todo(string $project_uuid, string $section_uuid, string $todo_uuid) {
    $db = get_db_connection_projects();
    $stmt = $db->prepare("DELETE FROM todos WHERE uuid = ?");
    $stmt->bindValue(1, $todo_uuid);
    $stmt->execute();
}


function mark_todo_done(string $project_uuid, string $section_uuid, string $todo_uuid) {
    $db = get_db_connection_projects();

    // Check current completion status
    $stmt = $db->prepare("SELECT completed FROM todos WHERE uuid = ?");
    $stmt->bindValue(1, $todo_uuid, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    // Fetch the result using fetchArray
    $todo = $result->fetchArray(SQLITE3_ASSOC);

    if ($todo) {
        // Toggle completion status
        if ($todo['completed'] == 1) {
            // If already marked, demark it
            $stmt = $db->prepare("UPDATE todos SET completed = 0, completed_when = NULL WHERE uuid = ?");
            $stmt->bindValue(1, $todo_uuid, SQLITE3_TEXT);
        } else {
            // If not marked, mark it as completed
            $stmt = $db->prepare("UPDATE todos SET completed = 1, completed_when = ? WHERE uuid = ?");
            $stmt->bindValue(1, date("Y-m-d H:i:s"), SQLITE3_TEXT);
            $stmt->bindValue(2, $todo_uuid, SQLITE3_TEXT);
        }

        $stmt->execute();
    }
}

?>
