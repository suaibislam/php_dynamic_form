<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Form with Modal</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container my-4">
    <h1 class="mb-4">Dynamic Form with Delete Button</h1>
    
    <!-- Submit Button -->
    <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#submitModal">Submit</button>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="submitModalLabel">Confirm Submission</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form id="dynamic-form">
      <div id="form-container">
        <!-- Initial Field Group -->
        <div class="row mb-3 field-group">
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
          <!-- Delete Button -->
          <div class="col-12 mt-2">
            <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
          </div>
        </div>
      </div>
      <!-- Add New Field Button -->
      <button type="button" id="add-field" class="btn btn-primary">Add New Field</button>
    </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirm-submit" class="btn btn-success">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function () {
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
            target.prop("disabled", false);
          },
        });
      };

      // Division-District-Thana Chain Update
      $("#form-container").on("change", ".division", function () {
        const divisionId = $(this).val();
        const districtSelect = $(this).closest(".field-group").find(".district");
        const thanaSelect = $(this).closest(".field-group").find(".thana");
        districtSelect.empty().prop("disabled", true);
        thanaSelect.empty().prop("disabled", true);
        if (divisionId) {
          fetchData("districts", divisionId, districtSelect);
        }
      });

      $("#form-container").on("change", ".district", function () {
        const districtId = $(this).val();
        const thanaSelect = $(this).closest(".field-group").find(".thana");
        thanaSelect.empty().prop("disabled", true);
        if (districtId) {
          fetchData("thanas", districtId, thanaSelect);
        }
      });

      // Add New Field Group
      $("#add-field").click(function () {
        const newFieldGroup = `
          <div class="row mb-3 field-group">
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
            <!-- Delete Button -->
            <div class="col-12 mt-2">
              <button type="button" class="btn btn-danger btn-sm remove-field">Remove</button>
            </div>
          </div>`;
        $("#form-container").append(newFieldGroup);
      });

      // Remove Field Group
      $("#form-container").on("click", ".remove-field", function () {
        $(this).closest(".field-group").remove();
      });

      // Submit Data
      $("#confirm-submit").click(function () {
        const formData = [];
        $(".field-group").each(function () {
          const division = $(this).find(".division").val();
          const district = $(this).find(".district").val();
          const thana = $(this).find(".thana").val();

          if (division && district && thana) {
            formData.push({ division, district, thana });
          }
        });

        $.ajax({
          url: "submit_data.php",
          method: "POST",
          data: { formData: JSON.stringify(formData) },
          success: (response) => {
            alert(response);
            $("#submitModal").modal("hide");
          },
        });
      });
    });
  </script>
</body>
</html>
