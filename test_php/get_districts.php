<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get division_id from the request
if (isset($_GET['division_id'])) {
    $division_id = (int) $_GET['division_id'];

    // Query to fetch districts based on division_id
    $result = $conn->query("SELECT id, name FROM districts WHERE division_id = $division_id");

    $districts = [];
    while ($row = $result->fetch_assoc()) {
        $districts[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    // Return districts as JSON
    echo json_encode($districts);
} else {
    echo json_encode([]);
}

$conn->close();
?>
