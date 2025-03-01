<?php
require_once "config.php";
require_once "userlib.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $users = load_users();
        foreach ($users as $user) {
            if ($user["email"] === $email && password_verify($password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["permissions"] = $user["permissions"];
                header("Location: projects.php");
                exit();
            }
        }
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="robots" content="noindex">
</head>
<body>
<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
        <h2 class="text-center"><i class="ti ti-login"></i> Login</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"> <?php echo htmlspecialchars($error); ?> </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <i class="ti ti-mail"></i> <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <i class="ti ti-key"></i> <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3"><a href="register.php">Don't have an account? Register</a></p>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#togglePassword").on("click", function () {
            var passField = $("#password");
            var type = passField.attr("type") === "password" ? "text" : "password";
            passField.attr("type", type);
            $(this).toggleClass("ti-eye ti-eye-off");
        });
    });
</script>
</body>
</html>
