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




    });