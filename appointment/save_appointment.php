<?php
session_start();
require_once('../includes/db_connection.php');
date_default_timezone_set('Asia/Kolkata');

// ✅ Load PHPMailer
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Validate required fields
$required_fields = ['patient_id', 'prefix', 'name', 'title', 'hf_name', 'gender', 'dob', 'mobile', 'address', 'city', 'doctor_id'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        die("❌ Missing required field: $field");
    }
}

// ✅ Sanitize and prepare variables
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
$status         = 'Pending';
$entry_datetime = date('Y-m-d H:i:s');


// ✅ Get staff info from session
$staff_id = $_SESSION['user_id'] ?? 0;
$staff_name = $_SESSION['username'] ?? '';

if (!$staff_name && $staff_id) {
    // Fallback from DB if session username not found
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$staff_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $staff_name = $user['username'] ?? 'UNKNOWN';
}

// Final fallback if still empty
if (empty($staff_name)) {
    $staff_name = 'UNKNOWN';
}

// ✅ Fetch doctor name (optional)
$doctor_stmt = $pdo->prepare("SELECT doctor_name FROM doctors WHERE id = ?");
$doctor_stmt->execute([$doctor_id]);
$doctor = $doctor_stmt->fetch();
$doctor_name = $doctor['doctor_name'] ?? '';



// ✅ Check if patient already exists
$check_stmt = $pdo->prepare("SELECT * FROM patients WHERE patient_id = ?");
$check_stmt->execute([$patient_id]);
$existing_patient = $check_stmt->fetch();

// ✅ Insert new patient if not found
if (!$existing_patient) {
    $insert_patient = $pdo->prepare("INSERT INTO patients (
        patient_id, prefix, name, title, hf_name, gender, dob, mobile, address, city, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $insert_patient->execute([
        $patient_id, $prefix, $name, $title, $hf_name, $gender, $dob,
        $mobile, $address, $city, $entry_datetime
    ]);
}

// ✅ Insert into appointments table (match 15 fields exactly)
try {
    $sql = "INSERT INTO appointments 
        (entry_datetime, patient_id, prefix, name, title, hf_name, gender, dob, address, city, mobile, staff_id, status, doctor_id, staff_name, doctor_name)
        VALUES 
        (NOW(), :patient_id, :prefix, :name, :title, :hf_name, :gender, :dob, :address, :city, :mobile, :staff_id, :status, :doctor_id, :staff_name, :doctor_name)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':patient_id'    => $patient_id,
        ':prefix'        => $prefix,
        ':name'          => $name,
        ':title'         => $title,
        ':hf_name'       => $hf_name,
        ':gender'        => $gender,
        ':dob'           => $dob,
        ':address'       => $address,
        ':city'          => $city,
        ':mobile'        => $mobile,
        ':staff_id'      => $staff_id,
        ':status'        => $status,
        ':doctor_id'     => $doctor_id,
        ':staff_name'    => $staff_name,
        ':doctor_name'   => $doctor_name
    ]);
} catch (PDOException $e) {
    die("❌ Appointment insert failed: " . $e->getMessage());
}


// ✅ Send confirmation email
$host = $_SERVER['HTTP_HOST'];
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mazktp@gmail.com'; // your email
    $mail->Password   = 'pknn vtca losh fenj'; // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('mazktp@gmail.com', 'Ansar Hospital');
    $mail->addAddress('pbon04@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Appointment Confirmation';

    $mail->addEmbeddedImage('D:/Projects/ansarhospital/kiratpur/static/images/logo_clinic.png', 'logoimg');

    $mail->Body = "
        <div style='border:1px solid #ccc; padding:20px; font-family:sans-serif; text-align:center;'>
            <img src='cid:logoimg' height='60' style='display:block; margin: 0 auto 10px;'><br>
            <h3 style='color:#0c5460;'>Ansar Hospital - Appointment Confirmation</h3>

            <table style='margin: 0 auto; width: 60%; border-collapse: collapse; text-align:left;' border='1'>
                <tr><th style='padding:8px;'>Patient ID</th><td style='padding:8px;'>$patient_id</td></tr>
                <tr><th style='padding:8px;'>Name</th><td style='padding:8px;'>$prefix $name</td></tr>
                <tr><th style='padding:8px;'>H/F Name</th><td style='padding:8px;'>$title $hf_name</td></tr>
                <tr><th style='padding:8px;'>Address</th><td style='padding:8px;'>$address, $city</td></tr>
                <tr><th style='padding:8px;'>Mobile</th><td style='padding:8px;'>$mobile</td></tr>
                <tr><th style='padding:8px;'>Doctor</th><td style='padding:8px;'>$doctor_name</td></tr>
            </table>

            <p style='margin-top:20px;'>This is an automated confirmation. Thank you for choosing <strong>Ansar Hospital</strong>.</p>
        </div>
    ";



    $mail->send();
    $msg = "✅ Appointment saved and email sent";
} catch (Exception $e) {
    $msg = "✅ Appointment saved, ❌ email failed";
}

// ✅ Redirect back to list
header("Location: http://$host/hospital_php/appointment/list.php?msg=" . urlencode($msg));
exit;
