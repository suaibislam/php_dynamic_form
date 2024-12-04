<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}
session_regenerate_id(true);

// Database connection
$conn = new mysqli("localhost", "root", "", "formdynamic");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add or update user
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Sanitize input data
//     $name = trim($_POST['name']);
//     $username = trim($_POST['username']);
//     $age = trim($_POST['age']);
//     $phone = trim($_POST['phone']);
//     $photo = null;
//     $division_id = isset($_POST['division_id']) ? $_POST['division_id'] : null;
//     $district_id = isset($_POST['district_id']) ? $_POST['district_id'] : null;
//     $thana_id = isset($_POST['thana_id']) ? $_POST['thana_id'] : null;

//     // Handle photo upload
//     if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
//         // Validate photo type (accept JPEG, PNG)
//         $allowedTypes = ['image/jpeg', 'image/png'];
//         $fileType = $_FILES['photo']['type'];
//         if (!in_array($fileType, $allowedTypes)) {
//             echo "<script>alert('Invalid photo type. Only JPEG and PNG are allowed.');</script>";
//             exit();
//         }

//         // Get file content and encode it to Base64
//         $fileContent = file_get_contents($_FILES['photo']['tmp_name']);
//         $photo = base64_encode($fileContent);
//     }


    

//     // Insert user
//     $stmt = $conn->prepare("INSERT INTO user_management (name, username, age, phone, photo, division_id, district_id, thana_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
//     if ($stmt === false) {
//         die("Error preparing query: " . $conn->error);
//     }

//     $stmt->bind_param("sssssiis", $name, $username, $age, $phone, $photo, $division_id, $district_id, $thana_id);

//     if ($stmt->execute()) {
//         echo "<script>alert('User saved successfully!');</script>";
//     } else {
//         echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
//     }
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Loop through submitted data
  foreach ($_POST['name'] as $index => $name) {
      $username = $_POST['username'][$index];
      $age = $_POST['age'][$index];
      $phone = $_POST['phone'][$index];
      $photo = null;
      $division_id = $_POST['division_id'][$index];
      $district_id = $_POST['district_id'][$index];
      $thana_id = $_POST['thana_id'][$index];
//form validation

 // Validate inputs
 if (empty($name) || empty($username)) {
  echo "<script>alert('Name and Username are required!');</script>";
  continue;
}

if ($age <= 0) {
  echo "<script>alert('Age must be greater than 0!');</script>";
  continue;
}

if (!empty($phone) && !preg_match('/^\d{11}$/', $phone)) {
  echo "<script>alert('Invalid phone number!');</script>";
  continue;
}
      // Handle photo upload
      if (isset($_FILES['photo']['tmp_name'][$index]) && $_FILES['photo']['error'][$index] === UPLOAD_ERR_OK) {
        $fileContent = file_get_contents($_FILES['photo']['tmp_name'][$index]);
        $photo = base64_encode($fileContent);
    }
      // Prepare and execute the insert statement
      $stmt = $conn->prepare("INSERT INTO user_management (name, username, age, phone, photo, division_id, district_id, thana_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssiis", $name, $username, $age, $phone, $photo, $division_id, $district_id, $thana_id);

      if (!$stmt->execute()) {
          echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "');</script>";
      }
  }
  echo "<script>alert('Users saved successfully!');</script>";
}


// Fetch all users
$result = $conn->query("SELECT * FROM user_management");

// Fetch division, district, and thana names
function getDivisionName($division_id) {
    global $conn;
    $result = $conn->query("SELECT name FROM divisions WHERE id = $division_id");
    $row = $result->fetch_assoc();
    return $row ? $row['name'] : '';
}

function getDistrictName($district_id) {
    global $conn;
    $result = $conn->query("SELECT name FROM districts WHERE id = $district_id");
    $row = $result->fetch_assoc();
    return $row ? $row['name'] : '';
}

function getThanaName($thana_id) {
    global $conn;
    $result = $conn->query("SELECT name FROM thanas WHERE id = $thana_id");
    $row = $result->fetch_assoc();
    return $row ? $row['name'] : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Form with Modal</title>
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
    <?php include "navbar.php" ?>
  <div class="container my-4">
    <h1 class="mb-4">Dynamic Form </h1>
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
                    $result = $conn->query("SELECT id, name FROM divisions");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
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
 <div class="table-responsive mt-4 container">
    <table class="table table-bordered">
      <thead>
        <tr>
          <!-- <th>ID</th> -->
          <th>Name</th>
          <th>Username</th>
          <th>Age</th>
          <th>Phone</th>
          <th>Photo</th>
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
            <!-- <td><?php/* echo $row['id']; */?></td> -->
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
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  // Add a custom validation method for phone numbers (example: 10 digits)
  $(document).ready(function () {

    // Add a custom validation method for phone numbers (example: 10 digits)
    function validatePhone(phone) {
        return /^\d{11}$/.test(phone);
    }

    // Form submission validation
    $("#dynamic-form").on("submit", function (e) {
        let isValid = true;

        $(".field-group").each(function () {
            const name = $(this).find("input[name='name[]']").val().trim();
            const username = $(this).find("input[name='username[]']").val().trim();
            const age = $(this).find("input[name='age[]']").val().trim();
            const phone = $(this).find("input[name='phone[]']").val().trim();
            const photo = $(this).find("input[name='photo[]']")[0].files[0];

            // Validate Name
            if (!name) {
                alert("Name is required!");
                isValid = false;
                return false; // Break out of .each()
            }

            // Validate Username
            if (!username) {
                alert("Username is required!");
                isValid = false;
                return false;
            }

            // Validate Age (must be a number and above 0)
            if (!age || isNaN(age) || parseInt(age) <= 0) {
                alert("Enter a valid age!");
                isValid = false;
                return false;
            }

            // Validate Phone
            if (phone && !validatePhone(phone)) {
                alert("Enter a valid phone number (11 digits)!");
                isValid = false;
                return false;
            }

            // Validate Photo (if provided)
            if (photo) {
                const allowedTypes = ["image/jpeg", "image/png"];
                if (!allowedTypes.includes(photo.type)) {
                    alert("Invalid photo type. Only JPEG and PNG are allowed.");
                    isValid = false;
                    return false;
                }
            }
        });

        if (!isValid) {
            e.preventDefault(); // Prevent form submission
        }
    });



      // Add field
      var fieldGroupCount = 1;
      $("#add-field").click(function() {
        fieldGroupCount++;
        var newFieldGroup = $(".field-group").first().clone();
        newFieldGroup.find("strong").text("Field Group " + fieldGroupCount);
        newFieldGroup.find("input, select").val("");  // Reset inputs
        $("#form-container").append(newFieldGroup);
      });

      $(document).on("click", ".remove-field", function() {
        if ($(".field-group").length > 1) {
          $(this).closest(".field-group").remove();
        }
      });




   

      // Update serial numbers
      function updateSerialNumbers() {
        $(".field-group").each(function(index) {
          $(this).find(".serial-number").text(index + 1);
        });
      }

    

      // Handle division selection change
      $(document).on('change', '.division', function() {
        var divisionId = $(this).val();
        var $districtSelect = $(this).closest('.field-group').find('.district');
        var $thanaSelect = $(this).closest('.field-group').find('.thana');
        $districtSelect.prop('disabled', false);
        $thanaSelect.prop('disabled', true);

        // Fetch districts
        if (divisionId) {
          $.ajax({
            url: 'get_districts.php',
            method: 'GET',
            data: { division_id: divisionId },
            success: function(data) {
              var districts = JSON.parse(data);
              $districtSelect.empty().append('<option value="">Select District</option>');
              $.each(districts, function(index, district) {
                $districtSelect.append('<option value="' + district.id + '">' + district.name + '</option>');
              });
            }
          });
        } else {
          $districtSelect.prop('disabled', true);
          $thanaSelect.prop('disabled', true);
        }
      });

      // Handle district selection change
      $(document).on('change', '.district', function() {
        var districtId = $(this).val();
        var $thanaSelect = $(this).closest('.field-group').find('.thana');
        $thanaSelect.prop('disabled', false);

        // Fetch thanas
        if (districtId) {
          $.ajax({
            url: 'get_thanas.php',
            method: 'GET',
            data: { district_id: districtId },
            success: function(data) {
              var thanas = JSON.parse(data);
              $thanaSelect.empty().append('<option value="">Select Thana</option>');
              $.each(thanas, function(index, thana) {
                $thanaSelect.append('<option value="' + thana.id + '">' + thana.name + '</option>');
              });
            }
          });
        } else {
          $thanaSelect.prop('disabled', true);
        }
      });

      // Submit form with modal
      $('#confirm-submit').click(function(e) {
        e.preventDefault();
        $('#dynamic-form').submit();
      });




    });
    
  </script>
</body>
</html>
