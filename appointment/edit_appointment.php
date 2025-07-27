<?php
require_once('../includes/db_connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$appointment_id = $_GET['id'];


// Get appointment + patient details
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Fetch dropdown values
$doctor_stmt = $pdo->query("SELECT id, doctor_name FROM doctors ORDER BY doctor_name");
$doctors = $doctor_stmt->fetchAll(PDO::FETCH_ASSOC);

$dropdown_stmt = $pdo->query("SELECT DISTINCT address FROM dropdownlist WHERE address IS NOT NULL AND address != '' ORDER BY address ASC");
$addresses = $dropdown_stmt->fetchAll(PDO::FETCH_COLUMN);

$city_stmt = $pdo->query("SELECT DISTINCT city FROM dropdownlist WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
$cities = $city_stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefix     = $_POST['prefix'];
    $name       = strtoupper(trim($_POST['name']));
    $title      = $_POST['title'];
    $hf_name    = strtoupper(trim($_POST['hf_name']));
    $gender     = $_POST['gender'];
    $dob        = $_POST['dob'];
    $mobile     = trim($_POST['mobile']);
    $address    = strtoupper(trim($_POST['address']));
    $city       = strtoupper(trim($_POST['city']));
    $doctor_id  = intval($_POST['doctor_id']);

    $doctor_name = '';
    $doc_stmt = $pdo->prepare("SELECT doctor_name FROM doctors WHERE id = ?");
    $doc_stmt->execute([$doctor_id]);
    $doc = $doc_stmt->fetch();
    if ($doc) $doctor_name = $doc['doctor_name'];

    // Update patients
    $update_patient = $pdo->prepare("UPDATE patients SET prefix=?, name=?, title=?, hf_name=?, gender=?, dob=?, mobile=?, address=?, city=? WHERE patient_id = ?");
    $update_patient->execute([
        $prefix, $name, $title, $hf_name, $gender, $dob, $mobile, $address, $city, $appointment['patient_id']
    ]);

    // Update appointment
    $update_appointment = $pdo->prepare("UPDATE appointments SET prefix=?, name=?, title=?, hf_name=?, gender=?, dob=?, mobile=?, address=?, city=?, doctor_id=?, doctor_name=? WHERE id = ?");
    $update_appointment->execute([
        $prefix, $name, $title, $hf_name, $gender, $dob, $mobile, $address, $city, $doctor_id, $doctor_name, $appointment_id
    ]);

    header("Location: hospital_php/appointment/edit_appointment.php?id=$appointment_id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .text-uppercase { text-transform: uppercase; }
        .card { border: 2px solid #0d6efd; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success text-center fw-bold m-3" role="alert">
        ✅ Appointment updated successfully!
    </div>
<?php endif; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            Edit Appointment
        </div>
        <div class="card-body">
            <form method="post" class="row g-3">

                <div class="col-md-2">
                    <label>Prefix</label>
                    <select name="prefix" class="form-control">
                        <?php foreach (['Mr.', 'Mrs.', 'Ms.', 'Master', 'Baby', 'Kumari', 'Sri', 'Smt'] as $p): ?>
                            <option value="<?= $p ?>" <?= $appointment['prefix'] === $p ? 'selected' : '' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control text-uppercase" value="<?= htmlspecialchars($appointment['name']) ?>" required>
                </div>
                <div class="col-md-2">
                    <label>Title</label>
                    <select name="title" class="form-control">
                        <?php foreach (['S/O', 'W/O', 'D/O', 'C/O'] as $t): ?>
                            <option value="<?= $t ?>" <?= $appointment['title'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>H/F Name</label>
                    <input type="text" name="hf_name" class="form-control text-uppercase" value="<?= htmlspecialchars($appointment['hf_name']) ?>" required>
                </div>
                <div class="col-md-3">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option <?= $appointment['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option <?= $appointment['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= htmlspecialchars($appointment['dob']) ?>">
                </div>
                <div class="col-md-3">
                    <label>Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($appointment['mobile']) ?>">
                </div>
                <div class="col-md-3">
                    <label>Address</label>
                    <select name="address" class="form-control text-uppercase">
                        <?php foreach ($addresses as $addr): ?>
                            <option value="<?= $addr ?>" <?= $appointment['address'] === $addr ? 'selected' : '' ?>><?= $addr ?></option>
                        <?php endforeach; ?>
                        <option value="<?= $appointment['address'] ?>" <?= !in_array($appointment['address'], $addresses) ? 'selected' : '' ?>><?= $appointment['address'] ?></option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>City</label>
                    <select name="city" class="form-control text-uppercase">
                        <?php foreach ($cities as $c): ?>
                            <option value="<?= $c ?>" <?= $appointment['city'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                        <option value="<?= $appointment['city'] ?>" <?= !in_array($appointment['city'], $cities) ? 'selected' : '' ?>><?= $appointment['city'] ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Doctor</label>
                    <select name="doctor_id" class="form-control" required>
                        <option value="">-- Select Doctor --</option>
                        <?php foreach ($doctors as $doc): ?>
                            <option value="<?= $doc['id'] ?>" <?= $appointment['doctor_id'] == $doc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doc['doctor_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-success">✅ Update Appointment</button>
                    <a href="/hospital_php/appointment/list.php" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
