<?php
require_once "config.php";
require_once "lib.php";

// Handle the project deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["uuid"])) {
  $uuid = $_POST["uuid"];
  delete_project($uuid); // Calls the function to delete the project
  header("Location: " . $_SERVER["PHP_SELF"]); // Refresh the page after deletion
  exit();
}

// Handle the project creation
if (
  $_SERVER["REQUEST_METHOD"] === "POST" &&
  isset($_POST["create_project"]) &&
  isset($_POST["project_name"])
) {
  $project_name = $_POST["project_name"];
  if (!empty($project_name)) {
    create_project($project_name); // Calls the function to create the project
    header("Location: " . $_SERVER["PHP_SELF"]); // Refresh the page after creation
    exit();
  } else {
    // Handle the error if the project name is empty
    echo '<script>alert("Project name cannot be empty!");</script>';
  }
}

// Get all projects
$projects = get_all_projects();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.30.0/dist/tabler-icons.min.css">
  <title>Projects</title>
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
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php foreach ($projects as $project) : ?>
        <div class="col">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($project["name"]); ?></h5>
              <a href="project.php?uuid=<?php echo htmlspecialchars($project["uuid"]); ?>" class="btn btn-primary">View Project</a>

              <!-- Delete Button -->
              <button class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo htmlspecialchars($project["uuid"]); ?>">
                <i class="ti ti-trash"></i>
              </button>
            </div>
            <div class="card-footer text-muted d-flex align-items-center" style="font-size: 0.9rem;">
              <i class="ti ti-calendar-plus" style="margin-right: 5px;"></i>
              <?php echo htmlspecialchars($project["created"]); ?>
            </div>
          </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteModal<?php echo htmlspecialchars($project["uuid"]); ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete this project?
              </div>
              <div class="modal-footer">
                <!-- Form to submit the delete request -->
                <form method="POST" action="">
                  <input type="hidden" name="uuid" value="<?php echo htmlspecialchars($project["uuid"]); ?>">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Green Floating Button -->
  <button class="floating-btn" data-bs-toggle="modal" data-bs-target="#createProjectModal">
    <i class="ti ti-plus"></i>
  </button>

  <!-- Modal for Project Creation -->
  <div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createProjectModalLabel">Create New Project</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="">
            <div class="mb-3">
              <label for="project_name" class="form-label">Project Name</label>
              <input type="text" name="project_name" id="project_name" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="create_project" class="btn btn-primary">Create Project</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
