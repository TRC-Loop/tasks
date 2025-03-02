<?php
require_once "config.php";

class UserData {
    public int $id;
    public string $name;
    public string $password_hash;
    public string $email;
    public array $permissions;
    public string $picture;
}

function get_db_connection() {
    global $USERS_DB;
    $db = new SQLite3($USERS_DB);
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT UNIQUE,
        password_hash TEXT,
        email TEXT,
        permissions TEXT,
        picture TEXT
    )");
    return $db;
}

function load_users(): array {
    $db = get_db_connection();
    $result = $db->query("SELECT * FROM users");
    $users = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $row["permissions"] = json_decode($row["permissions"], true);
        $users[] = $row;
    }
    return $users;
}

function save_users(array $users): void {
    // Not needed with SQLite as data is stored persistently.
}

function create_user(UserData $userData) {
    $db = get_db_connection();
    $stmt = $db->prepare("INSERT INTO users (name, password_hash, email, permissions, picture) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $userData->name);
    $stmt->bindValue(2, $userData->password_hash);
    $stmt->bindValue(3, $userData->email);
    $stmt->bindValue(4, json_encode($userData->permissions));
    $stmt->bindValue(5, $userData->picture);
    $stmt->execute();
    $userData->id = $db->lastInsertRowID();
    return $userData;
}

function delete_user(int $id) {
    $db = get_db_connection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bindValue(1, $id);
    $stmt->execute();
}

function get_user(int $id) {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bindValue(1, $id);
    $result = $stmt->execute();
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $row["permissions"] = json_decode($row["permissions"], true);
        return (object)$row;
    }
    return null;
}

function verify(string $username, string $password_hash) {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM users WHERE name = ? AND password_hash = ?");
    $stmt->bindValue(1, $username);
    $stmt->bindValue(2, $password_hash);
    $result = $stmt->execute();
    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $row["permissions"] = json_decode($row["permissions"], true);
        return (object)$row;
    }
    return false;
}
?>
