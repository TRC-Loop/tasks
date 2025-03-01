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

function load_users(): array {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    return json_decode(file_get_contents(USERS_FILE), true) ?? [];
}

function save_users(array $users): void {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function create_user(UserData $userData) {
    $users = load_users();
    $userData->id = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
    $users[] = (array)$userData;
    save_users($users);
    return $userData;
}

function delete_user(int $id) {
    $users = load_users();
    $users = array_filter($users, fn($user) => $user['id'] !== $id);
    save_users(array_values($users));
}

function get_user(int $id) {
    $users = load_users();
    foreach ($users as $user) {
        if ($user['id'] === $id) {
            return (object)$user;
        }
    }
    return null;
}

function verify(string $username, string $password_hash) {
    $users = load_users();
    foreach ($users as $user) {
        if ($user['name'] === $username && $user['password_hash'] === $password_hash) {
            return (object)$user;
        }
    }
    return false;
}
?>
