<?php
require_once "config.php";
require_once "lib.php";

// Get project and section UUIDs from the query string
$project_uuid = $_GET["project_uuid"] ?? null;
$section_uuid = $_GET["section_uuid"] ?? null;

if (!$project_uuid || !$section_uuid) {
  die("Project and Section UUIDs are required.");
}

// Handle todo creation
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["create_todo"]) &&
  isset($_POST["todo_name"])
) {
  $todo_name = $_POST["todo_name"];
  if (!empty($todo_name)) {
    create_todo($project_uuid, $section_uuid, $todo_name);
    header(
      "Location: section.php?project_uuid=" .
        urlencode($project_uuid) .
        "&section_uuid=" .
        urlencode($section_uuid)
    );
    exit();
  } else {
    $error_message = "Todo name cannot be empty.";
  }
}

// Handle todo deletion
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["delete_todo"]) &&
  isset($_POST["todo_uuid"])
) {
  $todo_uuid = $_POST["todo_uuid"];
  delete_todo($project_uuid, $section_uuid, $todo_uuid);
  header(
    "Location: section.php?project_uuid=" .
      urlencode($project_uuid) .
      "&section_uuid=" .
      urlencode($section_uuid)
  );
  exit();
}

// Handle marking todo as done (or not done)
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["complete_todo"]) &&
  isset($_POST["todo_uuid"])
) {
  $todo_uuid = $_POST["todo_uuid"];
  $completed = isset($_POST["completed"]) && $_POST["completed"] == "1"; // 1 for completed, 0 for not completed

  // Update the todo completion status in the database
  mark_todo_done($project_uuid, $section_uuid, $todo_uuid);

  header(
    "Location: section.php?project_uuid=" .
      urlencode($project_uuid) .
      "&section_uuid=" .
      urlencode($section_uuid)
  );
  exit();
}

// Handle todo name update
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["edit_todo"]) &&
  isset($_POST["todo_uuid"]) &&
  isset($_POST["new_todo_name"])
) {
  $todo_uuid = $_POST["todo_uuid"];
  $new_todo_name = $_POST["new_todo_name"];
  if (!empty($new_todo_name)) {
    update_todo_name($todo_uuid, $new_todo_name);
    header(
      "Location: section.php?project_uuid=" .
        urlencode($project_uuid) .
        "&section_uuid=" .
        urlencode($section_uuid)
    );
    exit();
  } else {
    $error_message = "Todo name cannot be empty.";
  }
}

// Get all todos for the section
$todos = get_all_todos($project_uuid, $section_uuid);

// Filter out deleted todos
$todos = array_filter($todos, function ($todo) {
  return !$todo["deleted"];
});

// Handle search
$search_term = $_GET["search"] ?? "";
if (!empty($search_term)) {
  $todos = array_filter($todos, function ($todo) use ($search_term) {
    return stripos($todo["name"], $search_term) !== false;
  });
}

// Sort todos so that completed tasks are at the bottom
usort($todos, function ($a, $b) {
  return $a["completed"] <=> $b["completed"];
});

// Get project and section details
$projects = get_all_projects();
$project = null;
foreach ($projects as $p) {
  if ($p["uuid"] === $project_uuid) {
    $project = $p;
    break;
  }
}

if (!$project) {
  die("Project not found.");
}

$sections = get_all_sections($project_uuid);
$section = null;
foreach ($sections as $s) {
  if ($s["uuid"] === $section_uuid) {
    $section = $s;
    break;
  }
}

if (!$section) {
  die("Section not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.30.0/dist/tabler-icons.min.css">
  <title>Section: <?php echo htmlspecialchars($section["name"]); ?></title>
  <style>
    /* Floating button on the side */
    .floating-btn {
      position: fixed;
      right: 20px;
      bottom: 20px;
      background-color: green;
      color: white;
      border: none;
      border-radius: 50%;
      padding: 15px 20px;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>

<body>
  <div class="container mt-4">
    <h1>Section: <?php echo htmlspecialchars($section["name"]); ?></h1>
    <a href="project.php?uuid=<?php echo htmlspecialchars($project_uuid); ?>" class="btn btn-secondary mb-3">
      <i class="ti ti-arrow-left"></i> Back to Project
    </a>

    <!-- Search Bar -->
    <form method="GET" action="" class="mb-3">
      <div class="input-group">
        <input type="hidden" name="project_uuid" value="<?php echo htmlspecialchars($project_uuid); ?>">
        <input type="hidden" name="section_uuid" value="<?php echo htmlspecialchars($section_uuid); ?>">
        <input type="text" class="form-control" placeholder="Search tasks..." name="search"
          value="<?php echo htmlspecialchars($search_term); ?>">
        <button class="btn btn-outline-secondary" type="submit" id="button-search">
          <i class="ti ti-search"></i>
        </button>
      </div>
    </form>

    <h2>Tasks</h2>

    <?php if (isset($error_message)) : ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <ul class="list-group mb-3">
      <?php if (empty($todos)) : ?>
      <li class="list-group-item">No tasks yet. Add one to get started!</li>
      <?php else : ?>
      <?php foreach ($todos as $todo) : ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <form method="POST" action="" class="d-inline">
            <input type="hidden" name="todo_uuid" value="<?php echo htmlspecialchars($todo["uuid"]); ?>">
            <input type="checkbox" name="completed" id="completed_<?php echo htmlspecialchars($todo["uuid"]); ?>"
              value="true" <?php echo $todo["completed"] ? "checked" : ""; ?> onchange="this.form.submit()">
            <label class="form-check-label <?php echo $todo["completed"] ? "text-decoration-line-through" : ""; ?>"
              for="completed_<?php echo htmlspecialchars($todo["uuid"]); ?>">
              <?php echo htmlspecialchars($todo["name"]); ?>
            </label>
            <input type="hidden" name="complete_todo" value="true">
          </form>
        </div>
        <div>
          <!-- Edit Button -->
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
            data-bs-target="#editModal<?php echo htmlspecialchars($todo["uuid"]); ?>">
            <i class="ti ti-edit"></i>
          </button>
          <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
            data-bs-target="#deleteModal<?php echo htmlspecialchars($todo["uuid"]); ?>">
            <i class="ti ti-trash"></i>
          </button>
        </div>
      </li>

      <!-- Modal for Edit Todo -->
      <div class="modal fade" id="editModal<?php echo htmlspecialchars($todo["uuid"]); ?>" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit Task Name</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="">
                <input type="hidden" name="todo_uuid" value="<?php echo htmlspecialchars($todo["uuid"]); ?>">
                <div class="mb-3">
                  <label for="new_todo_name" class="form-label">New Task Name</label>
                  <input type="text" class="form-control" id="new_todo_name" name="new_todo_name"
                    value="<?php echo htmlspecialchars($todo["name"]); ?>" required>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" name="edit_todo" class="btn btn-primary">Update Task</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal for Delete Confirmation -->
      <div class="modal fade" id="deleteModal<?php echo htmlspecialchars($todo["uuid"]); ?>" tabindex="-1"
        aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete this task?
            </div>
            <div class="modal-footer">
              <!-- Form to submit the delete request -->
              <form method="POST" action="">
                <input type="hidden" name="todo_uuid" value="<?php echo htmlspecialchars($todo["uuid"]); ?>">
                <input type="hidden" name="delete_todo" value="true">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>

  <!-- Green Floating Button -->
  <button class="floating-btn" data-bs-toggle="modal" data-bs-target="#createTodoModal">
    <i class="ti ti-plus"></i>
  </button>

  <!-- Modal for Todo Creation -->
  <div class="modal fade" id="createTodoModal" tabindex="-1" aria-labelledby="createTodoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createTodoModalLabel">Create New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (isset($error_message)) : ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="mb-3">
              <label for="todo_name" class="form-label">Task Name</label>
              <input type="text" class="form-control" id="todo_name" name="todo_name" required>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="create_todo" class="btn btn-primary">Create Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
