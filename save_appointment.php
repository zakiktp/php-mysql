<?php
session_start();
require_once 'db_connection.php';

date_default_timezone_set('Asia/Kolkata'); // or your actual time zone


// ✅ Email library
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sanitize and fetch POST data
$patient_id     = strtoupper(trim($_POST['patient_id']));
$prefix         = trim($_POST['prefix']);
$name           = strtoupper(trim($_POST['name']));
$title          = trim($_POST['title']);
$hf_name        = strtoupper(trim($_POST['hf_name']));
$gender         = trim($_POST['gender']);
$dob            = $_POST['dob'];
$mobile         = trim($_POST['mobile']);
$address        = strtoupper(trim($_POST['address']));
$city           = strtoupper(trim($_POST['city']));
$doctor_id      = intval($_POST['doctor_id']);
$status         = 'Active';
$entry_datetime = date('Y-m-d H:i:s');

$staff_id       = $_SESSION['staff_id'] ?? 0;
$staff_name     = $_SESSION['username'] ?? '';

// ✅ Get doctor name
$doctor_stmt = $pdo->prepare("SELECT doctor_name FROM doctors WHERE id = ?");
$doctor_stmt->execute([$doctor_id]);
$doctor = $doctor_stmt->fetch();
$doctor_name = $doctor ? $doctor['doctor_name'] : '';

// ✅ Check if patient already exists
$check_stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$check_stmt->execute([$patient_id]);
$existing_patient = $check_stmt->fetch();

if (!$existing_patient) {
    $insert_patient = $pdo->prepare("INSERT INTO patients (
        patient_id, prefix, name, title, hf_name, gender, dob, mobile, address, city, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert_patient->execute([
        $patient_id, $prefix, $name, $title, $hf_name, $gender, $dob,
        $mobile, $address, $city, $entry_datetime
    ]);
}

// ✅ Save appointment
$insert_appointment = $pdo->prepare("INSERT INTO appointments (
    entry_datetime, patient_id, prefix, name, title, hf_name, gender, dob, mobile, address, city,
    staff_id, staff_name, doctor_id, doctor_name, status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$insert_appointment->execute([
    $entry_datetime, $patient_id, $prefix, $name, $title, $hf_name, $gender, $dob, $mobile, $address, $city,
    $staff_id, $staff_name, $doctor_id, $doctor_name, $status
]);

// ✅ Now send email
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mazktp@gmail.com'; // ✅ Replace
    $mail->Password   = 'pknn vtca losh fenj';    // ✅ 16-digit App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('mazktp@gmail.com', 'Ansar Hospital');
    $mail->addAddress('pbon04@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Appointment Confirmation';

    $logo_url = 'https://i.postimg.cc/MTw0LnTV/logo-clinic.png';
    $mail->Body = "
        <div style='border:1px solid #ccc; padding:20px; font-family:sans-serif;'>
            <img src='$logo_url' height='60'><br><br>
            <h3 style='color:#0c5460;'>Ansar Hospital - Appointment Confirmation</h3>
            <table style='width:100%; border-collapse: collapse;' border='1'>
                <tr><th style='padding:5px;'>Patient ID</th><td style='padding:5px;'>$patient_id</td></tr>
                <tr><th style='padding:5px;'>Name</th><td style='padding:5px;'>$prefix $name</td></tr>
                <tr><th style='padding:5px;'>H/F Name</th><td style='padding:5px;'>$title $hf_name</td></tr>
                <tr><th style='padding:5px;'>Address</th><td style='padding:5px;'>$address, $city</td></tr>
                <tr><th style='padding:5px;'>Mobile</th><td style='padding:5px;'>$mobile</td></tr>
                <tr><th style='padding:5px;'>Doctor</th><td style='padding:5px;'>$doctor_name</td></tr>
            </table>
            <p style='margin-top:20px;'>This is an automated confirmation. Thank you for choosing Ansar Hospital.</p>
        </div>
    ";

    $mail->send();
    header("Location: index.php?msg=Appointment saved and email sent");
} catch (Exception $e) {
    header("Location: index.php?msg=Appointment saved, but email failed to send: {$mail->ErrorInfo}");
}
exit;
