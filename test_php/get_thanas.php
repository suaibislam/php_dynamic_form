<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get district_id from the request
if (isset($_GET['district_id'])) {
    $district_id = (int) $_GET['district_id'];

    // Query to fetch thanas based on district_id
    $result = $conn->query("SELECT id, name FROM thanas WHERE district_id = $district_id");

    $thanas = [];
    while ($row = $result->fetch_assoc()) {
        $thanas[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    // Return thanas as JSON
    echo json_encode($thanas);
} else {
    echo json_encode([]);
}

$conn->close();
?>
