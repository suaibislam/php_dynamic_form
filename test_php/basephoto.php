<?php
// Start session
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Get file content and encode it to Base64
        $fileContent = file_get_contents($_FILES['photo']['tmp_name']);
        $base64Photo = base64_encode($fileContent);

        // Save Base64 string into the database
        $stmt = $conn->prepare("INSERT INTO photos (photo) VALUES (?)");
        $stmt->bind_param("s", $base64Photo);
        if ($stmt->execute()) {
            echo "<script>alert('Photo uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
        }
    } else {
        echo "<script>alert('Please select a valid photo.');</script>";
    }
}

// Fetch all photos
$result = $conn->query("SELECT * FROM photos");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base64 Photo Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Base64 Photo Upload</h2>

        <!-- Photo Upload Form -->
        <form action="" method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="photo" class="form-label">Choose a Photo:</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Photo</button>
        </form>

        <!-- Display Photos -->
        <h3>Uploaded Photos</h3>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="data:image/jpeg;base64,<?= htmlspecialchars($row['photo']); ?>" alt="Photo" class="card-img-top">
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No photos found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
