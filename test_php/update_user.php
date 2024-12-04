<?php
// Centralized database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the input
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $age = trim($_POST['age']);
    $phone = trim($_POST['phone']);
    // $description = trim($_POST['description']);
    $photoName = null;

    // Handle file upload securely
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Validate image file type and size
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['photo']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            echo "Error: Only JPG, PNG, or GIF images are allowed.";
            exit();
        }

        // Get file content and encode it to Base64
        $fileContent = file_get_contents($_FILES['photo']['tmp_name']);
        $photoName = base64_encode($fileContent);
    }

    // Update user data
    $sql = "UPDATE user_management SET name=?, username=?, age=?, phone=?";

    if ($photoName) {
        $sql .= ", photo=?";
    }

    $sql .= " WHERE id=?";

    $stmt = $conn->prepare($sql);

    if ($photoName) {
        $stmt->bind_param("sssssi", $name, $username, $age, $phone, $photoName, $id);
    } else {
        $stmt->bind_param("ssssi", $name, $username, $age, $phone,  $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Error: " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
}
$conn->close();
?>
