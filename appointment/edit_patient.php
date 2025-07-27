<?php
require_once('../includes/db_connection.php');

$patient_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!$patient_id) {
    echo "Invalid request";
    exit;
}

// Fetch patient details securely using prepared statement
$stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient not found";
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim and sanitize inputs
    $prefix    = trim($_POST['prefix']);
    $name      = strtoupper(trim($_POST['name']));
    $title     = trim($_POST['title']);
    $hf_name   = strtoupper(trim($_POST['hf_name']));
    $gender    = $_POST['gender'];
    $dob       = $_POST['dob'];
    $address   = strtoupper(trim($_POST['address']));
    $city      = strtoupper(trim($_POST['city']));
    $mobile    = trim($_POST['mobile']);

    // Basic validation (add more if needed)
    if (!$name) $errors[] = "Name is required.";
    if (!$mobile) $errors[] = "Mobile is required.";
    if (!$gender) $errors[] = "Gender is required.";

    if (empty($errors)) {
        $update_stmt = $pdo->prepare("UPDATE patients SET 
            prefix = ?, name = ?, title = ?, hf_name = ?, gender = ?, dob = ?, address = ?, city = ?, mobile = ? 
            WHERE patient_id = ?");
        $update_stmt->execute([
            $prefix, $name, $title, $hf_name, $gender, $dob, $address, $city, $mobile, $patient_id
        ]);
        $success = "Patient details updated successfully.";
        // Refresh patient data
        $stmt->execute([$patient_id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    }
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

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="border p-4 bg-white rounded shadow-sm" novalidate>
        <div class="row mb-3">
            <div class="col">
                <label for="prefix">Prefix</label>
                <select id="prefix" name="prefix" class="form-control">
                    <?php
                    $prefixes = ['Mr.', 'Mrs.', 'Ms.', 'Master', 'Baby', 'Sri', 'Smt'];
                    foreach ($prefixes as $pfx) {
                        $sel = ($patient['prefix'] === $pfx) ? 'selected' : '';
                        echo "<option value=\"$pfx\" $sel>$pfx</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['name']) ?>" required>
            </div>
            <div class="col">
                <label for="mobile">Mobile <span class="text-danger">*</span></label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="<?= htmlspecialchars($patient['mobile']) ?>" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="title">Title</label>
                <select id="title" name="title" class="form-control">
                    <?php
                    $titles = ['S/O', 'D/O', 'W/O', 'C/O'];
                    foreach ($titles as $t) {
                        $sel = ($patient['title'] === $t) ? 'selected' : '';
                        echo "<option value=\"$t\" $sel>$t</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <label for="hf_name">Father/Husband Name</label>
                <input type="text" id="hf_name" name="hf_name" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['hf_name']) ?>">
            </div>
            <div class="col">
                <label for="gender">Gender <span class="text-danger">*</span></label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="Male" <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control" value="<?= htmlspecialchars($patient['dob']) ?>">
            </div>
            <div class="col">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['address']) ?>">
            </div>
            <div class="col">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control text-uppercase" value="<?= htmlspecialchars($patient['city']) ?>">
            </div>
        </div>

        <button type="submit" class="btn btn-success">💾 Save Changes</button>
        <a href="/hospital_php/appointment/patient_list.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    document.querySelectorAll('.text-uppercase').forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
</script>

</body>
</html>
