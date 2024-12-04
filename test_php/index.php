<?php
// session_start();
// if (!isset($_SESSION["user"])) {
//     header("Location: login.php");
//     exit();
// }
// session_regenerate_id(true);

// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle form submission (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle each user data submission
    if (isset($_FILES['photo']) && isset($_POST['name']) && isset($_POST['username'])) {
        foreach ($_POST['name'] as $index => $name) {
            $username = $_POST['username'][$index];
            $age = $_POST['age'][$index];
            $phone = $_POST['phone'][$index];
            $division_id = $_POST['division_id'][$index];
            $district_id = $_POST['district_id'][$index];
            $thana_id = $_POST['thana_id'][$index];

            $photo = null;
            // Handle photo upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'][$index] === UPLOAD_ERR_OK) {
                // Validate photo type (accept JPEG, PNG)
                $allowedTypes = ['image/jpeg', 'image/png'];
                $fileType = $_FILES['photo']['type'][$index];
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<script>alert('Invalid photo type. Only JPEG and PNG are allowed.');</script>";
                    exit();
                }

                // Get file content and encode it to Base64
                $fileContent = file_get_contents($_FILES['photo']['tmp_name'][$index]);
                $photo = base64_encode($fileContent);
            }

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO user_management (name, username, age, phone, photo, division_id, district_id, thana_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiis", $name, $username, $age, $phone, $photo, $division_id, $district_id, $thana_id);

            if ($stmt->execute()) {
                echo "<script>alert('User saved successfully!');</script>";
            } else {
                echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
            }
        }
    }
}

// Fetch divisions, districts, thanas for the form
function getDivisionOptions() {
    global $conn;
    $result = $conn->query("SELECT id, name FROM divisions");
    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .btn {
            margin: 2px 0;
        }

        .dynamic-field {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    

    <div class="container my-4">
        <h1 class="mb-4">Dynamic Form</h1>
        <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#submitModal">ADD Data</button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitModalLabel">Confirm Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dynamic-form" action="" method="POST" enctype="multipart/form-data">
                        <div id="form-container">
                            <div class="row mb-3 field-group">
                                <div class="col-12">
                                    <strong>Field Group 1</strong>
                                </div>
                                <div class="mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" id="name" name="name[]" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username">Username:</label>
                                    <input type="text" id="username" name="username[]" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="age">Age:</label>
                                    <input type="number" id="age" name="age[]" class="form-control" required min="1">
                                </div>
                                <div class="mb-3">
                                    <label for="phone">Phone:</label>
                                    <input type="text" id="phone" name="phone[]" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Choose a Photo:</label>
                                    <input type="file" name="photo[]" id="photo" class="form-control" accept="image/*">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select division" name="division_id[]">
                                        <option value="">Select Division</option>
                                        <?php
                                        $divisions = getDivisionOptions();
                                        foreach ($divisions as $division) {
                                            echo "<option value='{$division['id']}'>{$division['name']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select district" name="district_id[]" disabled>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select thana" name="thana_id[]" disabled>
                                        <option value="">Select Thana</option>
                                    </select>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-field" class="btn btn-primary">Add New Field</button>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="confirm-submit" class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Age</th>
                    <th>Phone</th>
                    <th>Division</th>
                    <th>District</th>
                    <th>Thana</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php $result = $conn->query("SELECT * FROM user_management"); ?>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['age']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td>
                <?php if ($row['photo']): ?>
                    <?php 
                        // Detect the MIME type of the image
                        $photoData = $row['photo'];
                        $mimeType = (substr($photoData, 0, 4) === 'iVBOR') ? 'image/png' : 'image/jpeg'; // Simple check
                        $imageSrc = 'data:' . $mimeType . ';base64,' . $photoData;
                    ?>
                    <img src="<?= $imageSrc ?>" width="50" height="50" />
                <?php else: ?>
                    No Photo
                <?php endif; ?>
            </td>
            <td><?php echo getDivisionName($row['division_id']); ?></td>
            <td><?php echo getDistrictName($row['district_id']); ?></td>
            <td><?php echo getThanaName($row['thana_id']); ?></td>
            <td>
                <a href="edit_form.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
    <?php } ?>
</tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            // Add dynamic fields
            $("#add-field").click(function () {
                var fieldGroup = $(".field-group:first").clone();
                fieldGroup.find("input").val("");
                fieldGroup.appendTo("#form-container");
            });

            // Remove dynamic fields
            $(document).on("click", ".remove-field", function () {
                $(this).closest(".field-group").remove();
            });

            // Fetch districts based on selected division
            $(".division").change(function () {
                var divisionId = $(this).val();
                if (divisionId) {
                    $.ajax({
                        url: 'get_districts.php',
                        type: 'GET',
                        data: { division_id: divisionId },
                        success: function (response) {
                            var districts = JSON.parse(response);
                            var districtSelect = $(this).closest(".field-group").find(".district");
                            districtSelect.empty().append('<option value="">Select District</option>');
                            $.each(districts, function (index, district) {
                                districtSelect.append('<option value="' + district.id + '">' + district.name + '</option>');
                            });
                            districtSelect.prop("disabled", false);
                        }
                    });
                }
            });

            // Fetch thanas based on selected district
            $(document).on("change", ".district", function () {
                var districtId = $(this).val();
                if (districtId) {
                    $.ajax({
                        url: 'get_thanas.php',
                        type: 'GET',
                        data: { district_id: districtId },
                        success: function (response) {
                            var thanas = JSON.parse(response);
                            var thanaSelect = $(this).closest(".field-group").find(".thana");
                            thanaSelect.empty().append('<option value="">Select Thana</option>');
                            $.each(thanas, function (index, thana) {
                                thanaSelect.append('<option value="' + thana.id + '">' + thana.name + '</option>');
                            });
                            thanaSelect.prop("disabled", false);
                        }
                    });
                }
            });

            // Submit the form via AJAX
            $("#dynamic-form").submit(function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: "", 
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert("Data submitted successfully!");
                        location.reload();
                    }
                });
            });
        });
    </script>
</body>
</html>
