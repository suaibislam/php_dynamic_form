<?php
session_start();

// Redirect user to index if they're already logged in
if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

require "database.php"; // Ensure this includes your DB connection

if (isset($_POST["register"])) {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["repeat_password"];

    // Validate inputs
    if (empty($fullName) || empty($email) || empty($password) || empty($passwordRepeat)) {
        echo "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif ($password !== $passwordRepeat) {
        echo "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        // Use md5 or sha256 to hash the password
        $hashedPassword = md5($password); // OR $hashedPassword = hash('sha256', $password);

        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='alert alert-danger'>Email already exists.</div>";
            } else {
                // Insert the user into the database
                $sql = "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $hashedPassword);
                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='alert alert-success'>You are registered successfully.</div>";
                        header("Location: login.php"); // Redirect to login page
                        exit();
                    } else {
                        echo "<div class='alert alert-danger'>Database error. Please try again.</div>";
                    }
                }
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
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require 'navbar.php' ?>
    <div class="container mt-5">
        <h2 class="mb-4">Register</h2>
        <form action="registration.php" method="post">
            <div class="form-group mb-3">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:" required>
            </div>
            <div class="form-group mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email:" required>
            </div>
            <div class="form-group mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password:" required minlength="8">
            </div>
            <div class="form-group mb-3">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password:" required>
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="register">
            </div>
        </form>
        <div class="mt-3">
            <p>Already Registered? <a href="login.php">Login Here</a></p>
        </div>
    </div>
</body>
</html>
