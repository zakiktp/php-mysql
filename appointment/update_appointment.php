<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_id = $_POST['appointment_id'];
    $patient_id     = $_POST['patient_id'];
    $prefix         = strtoupper(trim($_POST['prefix']));
    $name           = strtoupper(trim($_POST['name']));
    $title          = $_POST['title'];
    $hf_name        = strtoupper(trim($_POST['hf_name']));
    $gender         = $_POST['gender'];
    $dob            = $_POST['dob'];
    $mobile         = $_POST['mobile'];
    $address        = strtoupper(trim($_POST['address']));
    $city           = strtoupper(trim($_POST['city']));
    $staff_username = $_SESSION['username'] ?? 'UNKNOWN';
    $doctor_id      = $_POST['doctor_id'];
    $status         = $_POST['status'] ?? 'ACTIVE';

    // Get staff_id
    $stmt = $pdo->prepare("SELECT id FROM staff WHERE username = ?");
    $stmt->execute([$staff_username]);
    $staff_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $staff_id = $staff_row ? $staff_row['id'] : null;

    // If "Other" address entered
    if (isset($_POST['new_address']) && $_POST['new_address'] !== '') {
        $new_address = strtoupper(trim($_POST['new_address']));
        $check = $pdo->prepare("SELECT * FROM dropdownlist WHERE address = ?");
        $check->execute([$new_address]);
        if ($check->rowCount() === 0) {
            $ins = $pdo->prepare("INSERT INTO dropdownlist (address) VALUES (?)");
            $ins->execute([$new_address]);
        }
        $address = $new_address;
    }

    // If "Other" city entered
    if (isset($_POST['new_city']) && $_POST['new_city'] !== '') {
        $new_city = strtoupper(trim($_POST['new_city']));
        $check = $pdo->prepare("SELECT * FROM dropdownlist WHERE city = ?");
        $check->execute([$new_city]);
        if ($check->rowCount() === 0) {
            $ins = $pdo->prepare("INSERT INTO dropdownlist (city) VALUES (?)");
            $ins->execute([$new_city]);
        }
        $city = $new_city;
    }

    // Update patients table
    $update_patient = $pdo->prepare("UPDATE patients SET 
        prefix = ?, name = ?, title = ?, hf_name = ?, gender = ?, dob = ?, mobile = ?, address = ?, city = ?
        WHERE patient_id = ?");
    $update_patient->execute([
        $prefix, $name, $title, $hf_name, $gender, $dob, $mobile, $address, $city, $patient_id
    ]);

    // Update appointments table
    $update_appointment = $pdo->prepare("UPDATE appointments SET 
        patient_id = ?, staff_id = ?, doctor_id = ?, status = ?
        WHERE id = ?");
    $update_appointment->execute([
        $patient_id, $staff_id, $doctor_id, $status, $appointment_id
    ]);

    echo "<script>alert('✅ Appointment updated successfully.'); window.location.href='appointment/appointments_list.php';</script>";
    exit;
}
?>
