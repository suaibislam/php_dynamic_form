<?php
// Include database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if district_id is provided
if (isset($_GET['district_id'])) {
    $district_id = $_GET['district_id'];
    
    // Fetch thanas based on the district_id
    $stmt = $conn->prepare("SELECT id, name FROM thanas WHERE district_id = ?");
    $stmt->bind_param("i", $district_id);
    $stmt->execute();
    
    // Get result
    $result = $stmt->get_result();
    $thanas = [];
    
    while ($row = $result->fetch_assoc()) {
        $thanas[] = $row;
    }

    // Return thanas as JSON
    echo json_encode($thanas);
}

$conn->close();
?>
