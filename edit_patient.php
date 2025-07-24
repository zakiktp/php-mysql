<?php
require 'db_connection.php';

$patient_id = $_GET['id'] ?? '';
if (!$patient_id) {
    echo "Invalid request";
    exit;
}

// Fetch patient details
$stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefix    = trim($_POST['prefix']);
    $name      = strtoupper(trim($_POST['name']));
    $title     = trim($_POST['title']);
    $hf_name   = strtoupper(trim($_POST['hf_name']));
    $gender    = $_POST['gender'];
    $dob       = $_POST['dob'];
    $address   = strtoupper(trim($_POST['address']));
    $city      = strtoupper(trim($_POST['city']));
    $mobile    = trim($_POST['mobile']);

    $update_stmt = $pdo->prepare("UPDATE patients SET 
        prefix = ?, name = ?, title = ?, hf_name = ?, gender = ?, dob = ?, address = ?, city = ?, mobile = ? 
        WHERE patient_id = ?");
    $update_stmt->execute([
        $prefix, $name, $title, $hf_name, $gender, $dob, $address, $city, $mobile, $patient_id
    ]);

    header("Location: patients_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-control.text-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h4>Edit Patient (<?= htmlspecialchars($patient['patient_id']) ?>)</h4>
    <form method="POST" class="border p-4 bg-white rounded shadow-sm">
        <div class="row mb-3">
            <div class="col">
                <label>Prefix</label>
                <select name="prefix" class="form-control">
                    <option <?= $patient['prefix'] === 'Mr.' ? 'selected' : '' ?>>Mr.</option>
                    <option <?= $patient['prefix'] === 'Mrs.' ? 'selected' : '' ?>>Mrs.</option>
                    <option <?= $patient['prefix'] === 'Ms.' ? 'selected' : '' ?>>Ms.</option>
                    <option <?= $patient['prefix'] === 'Master' ? 'selected' : '' ?>>Master</option>
                    <option <?= $patient['prefix'] === 'Baby' ? 'selected' : '' ?>>Baby</option>
                    <option <?= $patient['prefix'] === 'Sri' ? 'selected' : '' ?>>Sri</option>
                    <option <?= $patient['prefix'] === 'Smt' ? 'selected' : '' ?>>Smt</option>
                </select>
            </div>
            <div class="col">
                <label>Name</label>
                <input type="text" name="name" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['name']) ?>">
            </div>
            <div class="col">
                <label>Mobile</label>
                <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($patient['mobile']) ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Title</label>
                <select name="title" class="form-control">
                    <option <?= $patient['title'] === 'S/O' ? 'selected' : '' ?>>S/O</option>
                    <option <?= $patient['title'] === 'D/O' ? 'selected' : '' ?>>D/O</option>
                    <option <?= $patient['title'] === 'W/O' ? 'selected' : '' ?>>W/O</option>
                    <option <?= $patient['title'] === 'C/O' ? 'selected' : '' ?>>C/O</option>
                </select>
            </div>
            <div class="col">
                <label>Father/Husband Name</label>
                <input type="text" name="hf_name" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['hf_name']) ?>">
            </div>
            <div class="col">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($patient['dob']) ?>">
            </div>
            <div class="col">
                <label>Address</label>
                <input type="text" name="address" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['address']) ?>">
            </div>
            <div class="col">
                <label>City</label>
                <input type="text" name="city" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['city']) ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-success">💾 Save Changes</button>
        <a href="patients_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<!-- JS to enforce uppercase typing -->
<script>
    document.querySelectorAll('.text-uppercase').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>

</body>
</html>
