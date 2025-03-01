<?php
require_once "config.php";
require_once "userlib.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST["name"]) ? trim($_POST["name"]) : "";
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
    $password = $_POST["password"] ?? "";
    $permissions = ["user"];
    $terms = isset($_POST["terms"]);

    // Email regex validation for max length of 320 characters
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!$terms) {
        $error = "You must accept the Terms of Service.";
    } elseif (strlen($name) > 20) {
        $error = "Username must be at most 20 characters long.";
    } elseif (strlen($password) > 128) {
        $error = "Password must be at most 128 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($email) > 320) {
        $error = "Email cannot exceed 320 characters.";
    } else {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $picture = "";

        if (!empty($_FILES["picture"]["tmp_name"])) {
            if ($_FILES["picture"]["size"] > 150000) {
                $error = "Image must be less than 150KB.";
            } else {
                $pictureData = file_get_contents($_FILES["picture"]["tmp_name"]);
                $picture = base64_encode($pictureData);
            }
        }

        if (!isset($error)) {
            $user = new UserData();
            $user->name = $name;
            $user->email = $email;
            $user->password_hash = $password_hash;
            $user->permissions = $permissions;
            $user->picture = $picture;

            create_user($user);
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <meta name="robots" content="noindex">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#email").on("input", function () {
                var email = $(this).val();
                var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (regex.test(email)) {
                    $(this).removeClass("is-invalid").addClass("is-valid");
                } else {
                    $(this).removeClass("is-valid").addClass("is-invalid");
                }
            });

            $("#password").on("input", function () {
                var strength = ["Weak", "Medium", "Strong"];
                var score = 0;
                var pass = $(this).val();

                if (pass.length > 6) score++;
                if (/[A-Z]/.test(pass) && /[a-z]/.test(pass)) score++;
                if (/\d/.test(pass) && /[^a-zA-Z0-9]/.test(pass)) score++;

                $("#password-strength").text(strength[Math.min(score, 2)]);
            });

            $("#togglePassword").on("click", function () {
                var passField = $("#password");
                var type = passField.attr("type") === "password" ? "text" : "password";
                passField.attr("type", type);
                $(this).toggleClass("ti-eye ti-eye-off");
            });

            $("#picture").on("change", function () {
                var file = this.files[0];
                if (file && file.size > 150000) {
                    alert("Image must be less than 150KB.");
                    $(this).val("");
                }
            });
        });
    </script>
</head>
<body>
<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
        <h2 class="text-center"><i class="ti ti-user-plus"></i> Register</h2>
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"> <?php echo htmlspecialchars($error); ?> </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <i class="ti ti-user-question"></i><label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" maxlength="20" required>
            </div>
            <div class="mb-3">
                <i class="ti ti-mail"></i><label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <i class="ti ti-key"></i><label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="password" maxlength="128" required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
                <div id="password-strength" class="mt-2 text-muted"></div>
            </div>
            <div class="mb-3">
                <i class="ti ti-camera-selfie"></i><label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" name="picture" id="picture" accept="image/*">
                <small class="text-muted">Max size: 150KB</small>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="terms" required>
                <i class="ti ti-gavel"></i><label class="form-check-label">I accept the <a href="#">Terms of Service</a></label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p style="color: lightgray; font-size: 12px; margin-top: 15px;">
                By default, you get a limit of 5 Projects, 2 Sections per Project, and 20 Todos per Section. If you need more, contact 
                <a href="mailto:ak@stellar-code.com" style="color: lightgray;">ak@stellar-code.com</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
