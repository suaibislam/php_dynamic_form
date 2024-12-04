<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Form with Modal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container my-4">
    <h1 class="mb-4">Dynamic Form with Delete Button</h1>
    <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#submitModal">Submit</button>
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
                  <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="username">Username:</label>
                  <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label for="age">Age:</label>
                  <input type="number" id="age" name="age" class="form-control" required min="1">
                </div>
                <div class="mb-3">
                  <label for="phone">Phone:</label>
                  <input type="text" id="phone" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                  <label for="photo" class="form-label">Choose a Photo:</label>
                  <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                </div>
                <div class="col-md-4">
                  <select class="form-select division">
                    <option value="">Select Division</option>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "formdynamic");
                    $result = $conn->query("SELECT id, name FROM divisions");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                    $conn->close();
                    ?>
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-select district" disabled>
                    <option value="">Select District</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <select class="form-select thana" disabled>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
      let fieldCounter = 1;

      // Fetch data for dropdowns
      const fetchData = (type, id, target) => {
        $.ajax({
          url: "fetch_data.php",
          method: "GET",
          data: { type, id },
          success: (response) => {
            const options = JSON.parse(response);
            target.empty().append(`<option value="">Select ${type.slice(0, -1)}</option>`);
            options.forEach((option) => {
              target.append(`<option value="${option.id}">${option.name}</option>`);
            });
            target.prop("disabled", false); // Enable the dropdown
          },
          error: (xhr, status, error) => {
            alert(`Error fetching ${type}: ${error}`);
          },
        });
      };

      // Event listeners for dropdown chaining
      $("#form-container").on("change", ".division", function () {
        const divisionId = $(this).val();
        const districtDropdown = $(this).closest(".field-group").find(".district");
        const thanaDropdown = $(this).closest(".field-group").find(".thana");

        districtDropdown.empty().append('<option value="">Select District</option>').prop("disabled", true);
        thanaDropdown.empty().append('<option value="">Select Thana</option>').prop("disabled", true);

        if (divisionId) {
          fetchData("districts", divisionId, districtDropdown);
        }
      });

      $("#form-container").on("change", ".district", function () {
        const districtId = $(this).val();
        const thanaDropdown = $(this).closest(".field-group").find(".thana");

        thanaDropdown.empty().append('<option value="">Select Thana</option>').prop("disabled", true);

        if (districtId) {
          fetchData("thanas", districtId, thanaDropdown);
        }
      });

      // Add a new field group
      $("#add-field").click(function () {
        fieldCounter++;
        const newFieldGroup = `
          <div class="row mb-3 field-group">
            <div class="col-12"><strong>Field Group ${fieldCounter}</strong></div>
            <div class="mb-3">
              <label for="name">Name:</label>
              <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="age">Age:</label>
              <input type="number" id="age" name="age" class="form-control" required min="1">
            </div>
            <div class="mb-3">
              <label for="phone">Phone:</label>
              <input type="text" id="phone" name="phone" class="form-control">
            </div>
            <div class="mb-3">
              <label for="photo" class="form-label">Choose a Photo:</label>
              <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
            </div>
            <div class="col-md-4">
              <select class="form-select division">
                <option value="">Select Division</option>
                <?php
                $conn = new mysqli("localhost", "root", "", "formdynamic");
                $result = $conn->query("SELECT id, name FROM divisions");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                $conn->close();
                ?>
              </select>
            </div>
            <div class="col-md-4">
              <select class="form-select district" disabled>
                <option value="">Select District</option>
              </select>
            </div>
            <div class="col-md-4">
              <select class="form-select thana" disabled>
                <option value="">Select Thana</option>
              </select>
            </div>
            <div class="col-12 mt-2">
              <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
            </div>
          </div>`;
        $("#form-container").append(newFieldGroup);
      });

      // Remove field group
      $("#form-container").on("click", ".remove-field", function () {
        $(this).closest(".field-group").remove();
      });

   
  // Handle form submission
  $("#dynamic-form").submit(function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    // Collect data for all field groups
    $(".field-group").each(function () {
      const field = {
        name: $(this).find("input[name='name']").val(),
        username: $(this).find("input[name='username']").val(),
        age: $(this).find("input[name='age']").val(),
        phone: $(this).find("input[name='phone']").val(),
        division: $(this).find("select.division").val(),
        district: $(this).find("select.district").val(),
        thana: $(this).find("select.thana").val(),
      };

      formData.append("formData[]", JSON.stringify(field));

      const photoInput = $(this).find("input[name='photo']");
      if (photoInput[0].files.length > 0) {
        formData.append("photo[]", photoInput[0].files[0]);
      }
    });

    // Submit via AJAX
    $.ajax({
      url: "submit_data.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        alert("Form submitted successfully!");
        location.reload();
      },
      error: function (xhr, status, error) {
        alert(`Error: ${xhr.responseText || error}`);
      },
    });
  });

    });
  </script>
</body>
</html>
