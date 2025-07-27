<?php
require_once('../includes/db_connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}


// Auto-generate next Patient ID like AH0001, AH0002, ...
try {
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(patient_id, 3) AS UNSIGNED)) AS max_id FROM patients");
    $max_id = $stmt->fetchColumn();

    if ($max_id) {
        $generated_patient_id = 'AH' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $generated_patient_id = 'AH0001';
    }
} catch (PDOException $e) {
    echo "Error generating Patient ID: " . $e->getMessage();
    exit;
}


// Dropdowns
$addresses = $pdo->query("SELECT DISTINCT address FROM patients ORDER BY address ASC")->fetchAll(PDO::FETCH_COLUMN);
$doctor_stmt = $pdo->query("SELECT id AS doctor_id, doctor_name FROM doctors ORDER BY doctor_name ASC");
$doctors = $doctor_stmt->fetchAll(PDO::FETCH_ASSOC);
$cities = $pdo->query("SELECT DISTINCT city FROM dropdownlist ORDER BY city ASC")->fetchAll(PDO::FETCH_COLUMN);

$staff_username = $_SESSION['username'] ?? 'UNKNOWN';
?>
<!DOCTYPE html>
<html>
<head>
    <title>New Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-row {
            margin-bottom: 10px;
        }
        .card {
            border: 3px solid #007bff;
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], select {
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#searchModal">
      🔍 Search Existing Patient
    </button>
    <h4><img src="icons/new.png"> New Appointment</h4>
  </div>

  <form action="save_appointment.php" method="POST">
    <div class="card p-3">
            <!-- Row 1 -->
            <div class="form-row">
                <div class="col">
                    <label>Patient ID</label>
                    <input type="text" name="patient_id" id="patient_id" class="form-control" value="<?= $generated_patient_id ?>" readonly>
                </div>
                <div class="col">
                    <label>Prefix</label>
                    <select class="form-control" id="prefix" name="prefix">
                        <option>--Select--</option>
                        <option>Mr.</option>
                        <option>Mrs.</option>
                        <option>Ms.</option>
                        <option>Master</option>
                        <option>Baby</option>
                    </select>
                </div>
                <div class="col">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>
            </div>

            <!-- Row 2 -->
            <div class="form-row">
                <div class="col">
                    <label>Title</label>
                    <select class="form-control" id="title" name="title">
                        <option>--Select--</option>
                        <option>S/O</option>
                        <option>W/O</option>
                        <option>D/O</option>
                        <option>C/O</option>
                    </select>
                </div>
                <div class="col">
                    <label>H/F Name</label>
                    <input type="text" name="hf_name" id="hf_name" class="form-control">
                </div>
                <div class="col">
                    <label>Gender</label>
                    <input type="text" name="gender" id="gender" class="form-control" readonly>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="form-row">
                <div class="col">
                    <label>Age (YY)</label>
                    <input type="text" name="age_yy" id="age_yy" class="form-control">
                </div>
                <div class="col">
                    <label>MM</label>
                    <input type="text" name="age_mm" id="age_mm" class="form-control">
                </div>
                <div class="col">
                    <label>DD</label>
                    <input type="text" name="age_dd" id="age_dd" class="form-control">
                </div>
                <div class="col">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="dob" class="form-control">
                </div>
                <div class="col">
                    <label>Mobile</label>
                    <input type="text" name="mobile" id="mobile" class="form-control">
                </div>
            </div>

            <!-- Row 4 -->
            <div class="form-row">
                <!-- Address -->
                <div class="form-group col-md-4">
                    <label for="address">Address</label>
                    <div class="input-group">
                        <select class="form-control" name="address" id="address" required>
                            <?php foreach ($addresses as $addr): ?>
                                <option value="<?= strtoupper($addr) ?>"><?= strtoupper($addr) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#addressModal">➕</button>
                        </div>
                    </div>
                </div>

                <!-- City -->
                <div class="form-group col-md-4">
                    <label for="city">City</label>
                    <div class="input-group">
                        <select class="form-control" name="city" id="city" required>
                            <?php foreach ($cities as $c): ?>
                                <option value="<?= strtoupper($c) ?>" <?= strtoupper($c) === 'KIRATPUR' ? 'selected' : '' ?>>
                                    <?= strtoupper($c) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#cityModal">➕</button>
                        </div>
                    </div>
                </div>

                <!-- Staff -->
                <div class="form-group col-md-4">
                    <label for="staff">Staff</label>
                    <input type="text" name="staff_id" id="staff" class="form-control" value="<?= $staff_username ?>" readonly>
                </div>
            </div>

            <!-- Row 5 -->
            <div class="form-row">
                <div class="col">
                    <label>Doctor</label>
                    <select class="form-control" name="doctor_id" id="doctor" required>
                        <option value="">SELECT</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['doctor_id'] ?>"><?= $doc['doctor_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="list.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="addressModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" id="newAddressInput" placeholder="Enter New Address">
        <button type="button" class="btn btn-primary btn-sm" onclick="addNewAddress()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- City Modal -->
<div class="modal fade" id="cityModal" tabindex="-1" role="dialog" aria-labelledby="cityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="cityModalLabel">Add New City</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control mb-2" id="newCityInput" placeholder="Enter New City">
        <button type="button" class="btn btn-primary btn-sm" onclick="addNewCity()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="searchModalLabel">Search Existing Patient</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="text" id="searchInput" class="form-control" placeholder="Enter at least 3 characters">
        <br>
        <div id="searchResults" class="table-responsive"></div>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
$('#prefix').change(function () {
    const val = $(this).val();
    $('#gender').val(['Mr.', 'Master'].includes(val) ? 'MALE' : ['Mrs.', 'Ms.', 'Baby'].includes(val) ? 'FEMALE' : '');
});

$('#dob').on('change', function () {
    const dob = new Date(this.value);
    const today = new Date();
    let yy = today.getFullYear() - dob.getFullYear();
    let mm = today.getMonth() - dob.getMonth();
    let dd = today.getDate() - dob.getDate();
    if (dd < 0) { mm--; dd += 30; }
    if (mm < 0) { yy--; mm += 12; }
    $('#age_yy').val(yy); $('#age_mm').val(mm); $('#age_dd').val(dd);
});

$('#age_yy, #age_mm, #age_dd').on('input', function () {
    const yy = +$('#age_yy').val() || 0;
    const mm = +$('#age_mm').val() || 0;
    const dd = +$('#age_dd').val() || 0;
    const today = new Date();
    const dob = new Date(today.getFullYear() - yy, today.getMonth() - mm, today.getDate() - dd);
    $('#dob').val(dob.toISOString().split('T')[0]);
});

function openAddressPopup() {
    const newAddr = prompt("Enter new address:");
    if (newAddr) {
        $.post('save_address.php', { address: newAddr }, function () {
            $('#address').append(`<option selected>${newAddr.toUpperCase()}</option>`);
        });
    }
}

$('#search_query').on('keyup', function () {
    $.post('search_patient.php', { query: $(this).val() }, function (data) {
        $('#search_results').html(data);
    });
});

document.getElementById("searchInput").addEventListener("keyup", function () {
  const query = this.value.trim();
  if (query.length < 3) {
    document.getElementById("searchResults").innerHTML = '';
    return;
  }

  fetch("search_patient_ajax.php?query=" + encodeURIComponent(query))
    .then(response => response.json())
    .then(data => {
      let html = '<table class="table table-bordered table-sm">';
      html += '<thead><tr><th>ID</th><th>Name</th><th>Father/Husband</th><th>Mobile</th><th>Action</th></tr></thead><tbody>';
      if (data.length === 0) {
        html += '<tr><td colspan="5">No matches found</td></tr>';
      } else {
        data.forEach(p => {
          html += `<tr>
            <td>${p.patient_id}</td>
            <td>${p.prefix} ${p.name}</td>
            <td>${p.title} ${p.hf_name}</td>
            <td>${p.mobile}</td>
            <td><button type="button" class="btn btn-primary btn-sm" onclick='fillPatientDetails(${JSON.stringify(p)})'>Select</button></td>
          </tr>`;
        });
      }
      html += '</tbody></table>';
      document.getElementById("searchResults").innerHTML = html;
    });
});

function fillPatientDetails(p) {
  document.getElementById("patient_id").value = p.patient_id;
  document.getElementById("prefix").value = p.prefix;
  document.getElementById("name").value = p.name;
  document.getElementById("title").value = p.title;
  document.getElementById("hf_name").value = p.hf_name;
  document.getElementById("gender").value = p.gender;
  document.getElementById("dob").value = p.dob;
  document.getElementById("mobile").value = p.mobile;
  document.getElementById("address").value = p.address;
  document.getElementById("city").value = p.city;

  // Calculate age from dob
  const dob = new Date(p.dob);
  const today = new Date();
  let age_yy = today.getFullYear() - dob.getFullYear();
  let age_mm = today.getMonth() - dob.getMonth();
  let age_dd = today.getDate() - dob.getDate();

  if (age_dd < 0) {
    age_mm--;
    age_dd += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
  }
  if (age_mm < 0) {
    age_yy--;
    age_mm += 12;
  }

  document.getElementById("age_yy").value = age_yy;
  document.getElementById("age_mm").value = age_mm;
  document.getElementById("age_dd").value = age_dd;

  // Reset Doctor to default (first)
  const doctorSelect = document.getElementById("doctor");
  if (doctorSelect && doctorSelect.options.length > 0) {
    doctorSelect.selectedIndex = 0;
  }

  // Close modal
  $('#searchModal').modal('hide');
}
function addNewAddress() {
  const input = document.getElementById("newAddressInput");
  let newAddress = input.value.trim().toUpperCase();
  if (newAddress === "") {
    alert("Please enter a valid address.");
    return;
  }

  fetch('save_address.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'address=' + encodeURIComponent(newAddress)
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      const select = document.getElementById("address");
      const option = document.createElement("option");
      option.value = newAddress;
      option.text = newAddress;
      option.selected = true;
      select.add(option);
      input.value = '';
      $('#addressModal').modal('hide');
    } else {
      alert("Failed to save address.");
    }
  });
}

function addNewCity() {
    const newCity = document.getElementById('newCityInput').value.trim();

    if (!newCity) {
        alert("Please enter a city name.");
        return;
    }

    // Send to server
    fetch('../appointment/save_city.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'city=' + encodeURIComponent(newCity)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // ✅ Insert new city into the form field
            const cityDropdown = document.getElementById('city');
            const newOption = document.createElement("option");
            newOption.value = newCity;
            newOption.text = newCity;
            newOption.selected = true; // Auto-select the new one
            cityDropdown.appendChild(newOption);

            // OR If you're using input instead of select:
            // document.getElementById('new_city').value = newCity;

            $('#cityModal').modal('hide');
            document.getElementById('newCityInput').value = '';
        } else {
            alert("❌ Failed to add city: " + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("❌ Error saving city.");
    });
}
</script>

</body>
</html>
