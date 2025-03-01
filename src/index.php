<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="robots" content="noindex">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.30.0/dist/tabler-icons.min.css">
  <title>Tasks - Private</title>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .private-notice {
      background: #dc3545;
      color: white;
      text-align: center;
      padding: 10px;
      font-weight: bold;
    }
    .card {
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="private-notice">Private Website - Unauthorized Access Prohibited</div>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <h1><i class="ti ti-list-check"></i> Welcome to Tasks</h1>
      <div>
        <a href="login.php" class="btn btn-primary"><i class="ti ti-login"></i> Login</a>
        <a href="register.php" class="btn btn-outline-secondary"><i class="ti ti-user-plus"></i> Register</a>
      </div>
    </div>
    
    <div class="mt-4">
      <p>Tasks is a open-source todolist web app.</p>
      <ul>
        <li><strong>Projects:</strong> Organize your tasks into different projects to keep everything structured.</li>
        <li><strong>Sections:</strong> Each project can have multiple sections to categorize tasks effectively.</li>
        <li><strong>Tasks:</strong> Manage individual tasks within each section, track progress, and stay productive.</li>
      </ul>
      <p>To start managing your tasks, please <a href="login.php">log in</a> or <a href="register.php">create an account</a>.</p>
    </div>
    
    <footer class="text-center mt-5 text-muted">
      <p>Contact: <a href="mailto:ak@stellar-code.com">ak@stellar-code.com</a></p>
    </footer>
  </div>
</body>
</html>
