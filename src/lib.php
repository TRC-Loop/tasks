<?php
require_once "config.php";

class ToDoData {
    public string $name;
    public string $uuid;
    public ?bool $completed = false;
    public string $created;
    public string $completed_when;
    public ?bool $deleted = false;
}

class SectionData {
    public ?array $todos = [];
    public string $uuid;
    public string $name;
    public string $created;
}

class ProjectData {
    public ?array $sections = [];
    public string $name;
    public string $created;
    public string $uuid;
}

function generate_uuid(): string {
    return bin2hex(random_bytes(32));
}

function create_project(string $name) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    $project = [
        "name" => $name,
        "created" => date("Y-m-d H:i:s"),
        "uuid" => generate_uuid(),
        "sections" => []
    ];
    
    $projects[] = $project;
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}

function get_all_projects() {
    global $file;
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function get_all_sections(string $project_uuid) {
    global $file;
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as $project) {
        if ($project["uuid"] === $project_uuid) {
            return $project["sections"];
        }
    }
    return [];
}

function get_all_todos(string $project_uuid, string $section_uuid) {
    global $file;
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as $project) {
        if ($project["uuid"] === $project_uuid) {
            foreach ($project["sections"] as $section) {
                if ($section["uuid"] === $section_uuid) {
                    return $section["todos"];
                }
            }
        }
    }
    return [];
}

function delete_project(string $uuid) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    $projects = array_filter($projects, fn($p) => $p["uuid"] !== $uuid);
    file_put_contents($file, json_encode(array_values($projects), JSON_PRETTY_PRINT));
}

function create_section(string $project_uuid, string $project_name) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as &$project) {
        if ($project["uuid"] === $project_uuid) {
            $section = [
                "name" => $project_name,
                "uuid" => generate_uuid(),
                "created" => date("Y-m-d H:i:s"),
                "todos" => []
            ];
            $project["sections"][] = $section;
            break;
        }
    }
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}

function delete_section(string $project_uuid, string $section_uuid) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as &$project) {
        if ($project["uuid"] === $project_uuid) {
            $project["sections"] = array_filter($project["sections"], fn($s) => $s["uuid"] !== $section_uuid);
            break;
        }
    }
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}

function create_todo(string $project_uuid, string $section_uuid, string $name) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as &$project) {
        if ($project["uuid"] === $project_uuid) {
            foreach ($project["sections"] as &$section) {
                if ($section["uuid"] === $section_uuid) {
                    $todo = [
                        "name" => $name,
                        "uuid" => generate_uuid(),
                        "created" => date("Y-m-d H:i:s"),
                        "completed" => false,
                        "completed_when" => "",
                        "deleted" => false
                    ];
                    $section["todos"][] = $todo;
                    break 2;
                }
            }
        }
    }
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}

function delete_todo(string $project_uuid, string $section_uuid, string $todo_uuid) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as &$project) {
        if ($project["uuid"] === $project_uuid) {
            foreach ($project["sections"] as &$section) {
                if ($section["uuid"] === $section_uuid) {
                    foreach ($section["todos"] as &$todo) {
                        if ($todo["uuid"] === $todo_uuid) {
                            $todo["deleted"] = true;
                            break 3;
                        }
                    }
                }
            }
        }
    }
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}

function mark_todo_done(string $project_uuid, string $section_uuid, string $todo_uuid) {
    global $file;
    
    $projects = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    
    foreach ($projects as &$project) {
        if ($project["uuid"] === $project_uuid) {
            foreach ($project["sections"] as &$section) {
                if ($section["uuid"] === $section_uuid) {
                    foreach ($section["todos"] as &$todo) {
                        if ($todo["uuid"] === $todo_uuid) {
                            $todo["completed"] = true;
                            $todo["completed_when"] = date("Y-m-d H:i:s");
                            break 3;
                        }
                    }
                }
            }
        }
    }
    file_put_contents($file, json_encode($projects, JSON_PRETTY_PRINT));
}
?>
