<!-- index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dynamic Form with MySQL</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container my-4">
    <h1 class="mb-4">Dynamic Form with MySQL</h1>
    
    <div id="form-container">
      <!-- Initial Field Group -->
      <div class="row mb-3 field-group">
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
      </div>
    </div>

    <!-- Add New Field Button -->
    <button id="add-field" class="btn btn-primary">Add New Field</button>
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

      $("#add-field").click(function () {
        const newFieldGroup = `
          <div class="row mb-3 field-group">
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
          </div>`;
        $("#form-container").append(newFieldGroup);
      });
    });
  </script>
</body>
</html>
