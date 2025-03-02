<?php
require_once "config.php";
require_once "lib.php";

// Get the project UUID from the query string
$project_uuid = $_GET["uuid"] ?? null;

if (!$project_uuid) {
  die("Project UUID is required.");
}

// Handle section creation
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["create_section"]) &&
  isset($_POST["section_name"])
) {
  $section_name = $_POST["section_name"];
  if (!empty($section_name)) {
    create_section($project_uuid, $section_name);
    header("Location: project.php?uuid=" . urlencode($project_uuid));
    exit();
  } else {
    $error_message = "Section name cannot be empty.";
  }
}

// Handle section deletion
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["delete_section"]) &&
  isset($_POST["section_uuid"])
) {
  $section_uuid = $_POST["section_uuid"];
  delete_section($project_uuid, $section_uuid);
  header("Location: project.php?uuid=" . urlencode($project_uuid));
  exit();
}

// Get project details (you might want to add a function for this in lib.php)
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

// Get all sections for the project
$sections = get_all_sections($project_uuid);

// Handle search
$search_term = $_GET["search"] ?? "";
if (!empty($search_term)) {
  $sections = array_filter($sections, function ($section) use ($search_term) {
    return stripos($section["name"], $search_term) !== false;
  });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.30.0/dist/tabler-icons.min.css">
  <title>Project: <?php echo htmlspecialchars($project["name"]); ?></title>
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
    <h1>Project: <?php echo htmlspecialchars($project["name"]); ?></h1>
    <a href="projects.php" class="btn btn-secondary mb-3">
      <i class="ti ti-arrow-left"></i> Back to Projects
    </a>

    <!-- Search Bar -->
    <form method="GET" action="" class="mb-3">
      <div class="input-group">
        <input type="hidden" name="uuid" value="<?php echo htmlspecialchars($project_uuid); ?>">
        <input type="text" class="form-control" placeholder="Search sections..." name="search"
          value="<?php echo htmlspecialchars($search_term); ?>">
        <button class="btn btn-outline-secondary" type="submit" id="button-search">
          <i class="ti ti-search"></i>
        </button>
      </div>
    </form>

    <div class="row row-cols-1 row-cols-md-2 g-4">
      <?php if (empty($sections)): ?>
        <div class="col">
          <p>No sections yet. Create one to get started!</p>
        </div>
      <?php else: ?>
        <?php foreach ($sections as $section): ?>
          <?php
          // Get the count of todos in this section
          $todos = get_all_todos($project_uuid, $section['uuid']);
          $todo_count = count($todos);

          // Get the count of completed todos in this section
          $completed_todos = array_filter($todos, function($todo) {
            return $todo['completed'] == 1;  // Check if the todo is completed
          });
          $completed_count = count($completed_todos);  // Count completed todos
          ?>
          <div class="col">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($section["name"]); ?></h5>
                <a href="section.php?project_uuid=<?php echo htmlspecialchars($project_uuid); ?>&section_uuid=<?php echo htmlspecialchars($section["uuid"]); ?>"
                  class="btn btn-primary">View Section</a>

                <!-- Delete Button -->
                <button class="btn btn-danger ms-2" data-bs-toggle="modal"
                  data-bs-target="#deleteModal<?php echo htmlspecialchars($section["uuid"]); ?>">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
              <div class="card-footer text-muted d-flex align-items-center" style="font-size: 0.9rem;">
                <i class="ti ti-calendar-plus" style="margin-right: 5px;"></i>
                <?php echo htmlspecialchars($section["created"]); ?>

                <!-- Completed todos out of total todos -->
                <span class="ms-auto d-flex align-items-center">
                  <i class="ti ti-checkbox" style="margin-right: 5px;"></i>
                  <span><?php echo $completed_count . " / " . $todo_count; ?> completed</span>
                </span>
              </div>
            </div>
          </div>

          <!-- Modal for Delete Confirmation -->
          <div class="modal fade" id="deleteModal<?php echo htmlspecialchars($section["uuid"]); ?>" tabindex="-1"
            aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  Are you sure you want to delete the section "<?php echo htmlspecialchars($section["name"]); ?>"?
                </div>
                <div class="modal-footer">
                  <!-- Form to submit the delete request -->
                  <form method="POST" action="">
                    <input type="hidden" name="section_uuid" value="<?php echo htmlspecialchars($section["uuid"]); ?>">
                    <input type="hidden" name="delete_section" value="true">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Green Floating Button -->
  <button class="floating-btn" data-bs-toggle="modal" data-bs-target="#createSectionModal">
    <i class="ti ti-plus"></i>
  </button>

  <!-- Modal for Section Creation -->
  <div class="modal fade" id="createSectionModal" tabindex="-1" aria-labelledby="createSectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createSectionModalLabel">Create New Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
          <?php endif; ?>
          <form method="POST" action="">
            <div class="mb-3">
              <label for="section_name" class="form-label">Section Name</label>
              <input type="text" class="form-control" id="section_name" name="section_name" required>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="create_section" class="btn btn-primary">Create Section</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
