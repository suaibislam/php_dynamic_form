<?php
// Centralized database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details for the form
$id = intval($_GET['updateid']);
$result = $conn->query("SELECT * FROM user_management WHERE id=$id");
$user = $result->fetch_assoc();
if (!$user) {
    die("User not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Update User</title>
</head>
<body>
    <?php include 'navbar.php' ?>
<div class="container mt-5">
    <h2>Update User</h2>
    <form action="update_user.php" method="POST" enctype="multipart/form-data" id="updateForm" onsubmit="return validateForm()">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']); ?>">

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="age" class="form-label">Age:</label>
            <input type="number" name="age" class="form-control" id="age" value="<?= htmlspecialchars($user['age']); ?>" required min="1">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone:</label>
            <input type="text" name="phone" class="form-control" id="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo:</label>
            <input type="file" name="photo" class="form-control" id="photo">
            <?php if ($user['photo']): ?>
                <img src="data:image/jpeg;base64,<?= htmlspecialchars($user['photo']); ?>" alt="Photo" class="img-fluid rounded" width="50">            
            <?php endif; ?>
        </div>

        <!-- <div class="mb-3">
            <label for="description" class="form-label">Description:</label>
            <textarea name="description" class="form-control" id="description" rows="4">= htmlspecialchars($user['description']); ?></textarea>
        </div> -->

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="user_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript Form Validation
    function validateForm() {
        const name = document.getElementById("name").value;
        const username = document.getElementById("username").value;
        const age = document.getElementById("age").value;
        const phone = document.getElementById("phone").value;

        // Check if Name and Username are filled
        if (!name || !username) {
            alert("Name and Username are required.");
            return false;
        }

        // Check if Age is a positive number
        if (age <= 0 || isNaN(age)) {
            alert("Please enter a valid age.");
            return false;
        }

        // Phone validation (simple check for 10 digits)
        const phoneRegex = /^[0-9]{11}$/;
        if (!phone.match(phoneRegex)) {
            alert("Phone number must be 11 digits.");
            return false;
        }

        return true;
    }
</script>
</body>
</html>
