<?php
require_once('../includes/db_connection.php');

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$id = $_GET['id'];

$sql = "SELECT 
            a.*, 
            p.prefix, p.name, p.title, p.hf_name, p.gender, p.dob, p.mobile, p.address, p.city,
            d.doctor_name,
            s.staff_name AS staff_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        LEFT JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN staff s ON a.staff_id = s.id
        WHERE a.id = ?";


$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    echo "Appointment not found.";
    exit;
}

// Fallback Age Calculation if age fields are missing
$age_str = '-';
if (!empty($appointment['dob']) && $appointment['dob'] !== '0000-00-00') {
    $dob = new DateTime($appointment['dob']);
    $today = new DateTime();
    $diff = $today->diff($dob);
    $age_str = "{$diff->y}Y {$diff->m}M {$diff->d}D";
} elseif (isset($appointment['age_yy']) || isset($appointment['age_mm']) || isset($appointment['age_dd'])) {
    $age_str = (int)($appointment['age_yy'] ?? 0) . 'Y ' .
               (int)($appointment['age_mm'] ?? 0) . 'M ' .
               (int)($appointment['age_dd'] ?? 0) . 'D';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Appointment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f2f6fa;
        }
        .card {
            border: 2px solid #007bff;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        table th {
            width: 30%;
            background-color: #f0f0f0;
            font-weight: 600;
        }
        table td {
            background-color: #fff;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header text-center">
            Appointment Details
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Patient ID</th><td><?= htmlspecialchars($appointment['patient_id']) ?></td></tr>
                <tr><th>Prefix</th><td><?= htmlspecialchars($appointment['prefix']) ?></td></tr>
                <tr><th>Name</th><td><?= htmlspecialchars($appointment['name']) ?></td></tr>
                <tr><th>Title</th><td><?= htmlspecialchars($appointment['title']) ?></td></tr>
                <tr><th>H/F Name</th><td><?= htmlspecialchars($appointment['hf_name']) ?></td></tr>
                <tr><th>Gender</th><td><?= htmlspecialchars($appointment['gender']) ?></td></tr>
                <tr><th>Age</th><td><?= $age_str ?></td></tr>
                <tr><th>DOB</th><td><?= htmlspecialchars($appointment['dob']) ?></td></tr>
                <tr><th>Mobile</th><td><?= htmlspecialchars($appointment['mobile']) ?></td></tr>
                <tr><th>Address</th><td><?= htmlspecialchars($appointment['address']) ?></td></tr>
                <tr><th>City</th><td><?= htmlspecialchars($appointment['city']) ?></td></tr>
                <tr><th>Doctor</th><td><?= htmlspecialchars($appointment['doctor_name']) ?></td></tr>
                <tr><th>Staff</th><td><?= htmlspecialchars($appointment['staff_name']) ?></td></tr>
                <tr><th>Status</th><td><?= htmlspecialchars($appointment['status']) ?></td></tr>
            </table>
            <div class="text-end mt-3">
                <a href="/hospital_php/appointment/list.php" class="btn btn-secondary">← Back to Appointments</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
