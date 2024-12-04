<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "formdynamic";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if formData is provided in the POST request
    if (isset($_POST['formData'])) {
        // Decode the form data from JSON
        $formData = json_decode($_POST['formData'], true);

        // Check if json_decode() succeeded
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Invalid JSON format: " . json_last_error_msg();
            exit();
        }

        // Check for uploaded photos
        $uploadedPhotos = $_FILES['photo'] ?? null;

        // Iterate over each form group and insert data
        foreach ($formData as $index => $data) {
            // Sanitize and validate input data
            $division_id = intval($data['division']);
            $district_id = intval($data['district']);
            $thana_id = intval($data['thana']);
            $name = mysqli_real_escape_string($conn, $data['name']);
            $username = mysqli_real_escape_string($conn, $data['username']);
            $age = intval($data['age']);
            $phone = mysqli_real_escape_string($conn, $data['phone']);

            // Initialize photo path as empty
            $photo = '';

            // Handle photo upload if present
            if (
                $uploadedPhotos &&
                isset($uploadedPhotos['name'][$index]) &&
                $uploadedPhotos['error'][$index] === UPLOAD_ERR_OK
            ) {
                // Validate photo type
                $allowedTypes = ['image/jpeg', 'image/png'];
                $fileType = $uploadedPhotos['type'][$index];

                if (!in_array($fileType, $allowedTypes)) {
                    echo "Invalid photo type for entry $index. Only JPEG and PNG are allowed.";
                    exit();
                }

                // Define the upload directory
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create directory if not exists
                }

                // Generate a unique file name to avoid conflicts
                $photoName = time() . "_" . basename($uploadedPhotos['name'][$index]);
                $targetPath = $uploadDir . $photoName;

                // Move the uploaded file to the target directory
                if (!move_uploaded_file($uploadedPhotos['tmp_name'][$index], $targetPath)) {
                    echo "File upload failed for entry $index. Please try again.";
                    exit();
                }

                $photo = $targetPath; // Store file path in the database
            }

            // Prepare SQL query
            $sql = "INSERT INTO user_management (name, username, age, phone, photo, division_id, district_id, thana_id) 
                    VALUES ('$name', '$username', $age, '$phone', '$photo', $division_id, $district_id, $thana_id)";

            // Execute SQL query and check for errors
            if (!$conn->query($sql)) {
                echo "Error inserting data for entry $index: " . $conn->error;
                exit();
            }
        }

        // Return a success message
        echo "Form data submitted successfully!";
    } else {
        echo "No form data received!";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
