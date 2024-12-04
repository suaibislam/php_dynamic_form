<?php
session_start();

// Redirect to index if already logged in
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

require "database.php"; // Ensure this includes your DB connection

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        echo "<div class='alert alert-danger'>Both email and password are required.</div>";
    } else {
        // Use md5 or sha256 to hash the entered password (same method as in registration)
        $hashedPassword = md5($password); // OR $hashedPassword = hash('sha256', $password);

        // Query to fetch the user by email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if ($user) {
                // Check if the password matches the hash
                if ($hashedPassword == $user['password']) {
                    // Successful login, create session
                    $_SESSION["user"] = "yes";
                    header("Location: index.php"); // Redirect to index page
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Incorrect password.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Email not found.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require 'navbar.php' ?>
    <div class="container mt-5">
        <h2 class="mb-4">Login</h2>
        <!-- Login Form -->
        <form action="login.php" method="post">
            <div class="form-group mb-3">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group mb-3">
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-btn">
                <input type="submit" value="Login" name="login" class="btn btn-primary">
            </div>
        </form>
        <div class="mt-3">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
