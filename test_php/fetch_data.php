<?php
// fetch_data.php
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

$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($type === "districts") {
    $stmt = $conn->prepare("SELECT id, name FROM districts WHERE division_id = ?");
    $stmt->bind_param("i", $id);
} elseif ($type === "thanas") {
    $stmt = $conn->prepare("SELECT id, name FROM thanas WHERE district_id = ?");
    $stmt->bind_param("i", $id);
} else {
    echo json_encode([]);
    $conn->close();
    exit;
}

$stmt->execute();
$result = $stmt->get_result();
$options = [];
while ($row = $result->fetch_assoc()) {
    $options[] = $row;
}
echo json_encode($options);

$stmt->close();
$conn->close();
?>
