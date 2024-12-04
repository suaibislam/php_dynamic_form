<?php
// Include database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if division_id is provided
if (isset($_GET['division_id'])) {
    $division_id = $_GET['division_id'];
    
    // Fetch districts based on the division_id
    $stmt = $conn->prepare("SELECT id, name FROM districts WHERE division_id = ?");
    $stmt->bind_param("i", $division_id);
    $stmt->execute();
    
    // Get result
    $result = $stmt->get_result();
    $districts = [];
    
    while ($row = $result->fetch_assoc()) {
        $districts[] = $row;
    }

    // Return districts as JSON
    echo json_encode($districts);
}

$conn->close();
?>
