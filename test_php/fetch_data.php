<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch districts based on division
if (isset($_POST['division_id']) && !empty($_POST['division_id'])) {
    $division_id = intval($_POST['division_id']);
    
    // Query to get districts based on division_id
    $query = "SELECT id, name FROM districts WHERE division_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $division_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Create option elements for districts
        echo "<option value=''>Select District</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
    } else {
        echo "<option value=''>No districts found</option>";
    }
    exit();
}

// Fetch thanas based on district
if (isset($_POST['district_id']) && !empty($_POST['district_id'])) {
    $district_id = intval($_POST['district_id']);
    
    // Query to get thanas based on district_id
    $query = "SELECT id, name FROM thanas WHERE district_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $district_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Create option elements for thanas
        echo "<option value=''>Select Thana</option>";
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
    } else {
        echo "<option value=''>No thanas found</option>";
    }
    exit();
}
?>
