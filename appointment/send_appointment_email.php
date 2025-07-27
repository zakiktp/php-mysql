<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Sample data from form
$appointment = [
    'patient_id'   => $_POST['patient_id'] ?? 'N/A',
    'prefix'       => $_POST['prefix'] ?? '',
    'name'         => $_POST['name'] ?? '',
    'title'        => $_POST['title'] ?? '',
    'hf_name'      => $_POST['hf_name'] ?? '',
    'address'      => $_POST['address'] ?? '',
    'city'         => $_POST['city'] ?? '',
    'mobile'       => $_POST['mobile'] ?? '',
    'doctor_name'  => $_POST['doctor_name'] ?? 'Not Assigned',
];

// Variables for easy use
$patient_id   = $appointment['patient_id'];
$prefix       = $appointment['prefix'];
$name         = $appointment['name'];
$title        = $appointment['title'];
$hf_name      = $appointment['hf_name'];
$address      = $appointment['address'];
$city         = $appointment['city'];
$mobile       = $appointment['mobile'];
$doctor_name  = $appointment['doctor_name'];

$entry_datetime = date('Y-m-d H:i:s');
$appointment_time = date('d-m-Y h:i A', strtotime($entry_datetime));
$logo_url = 'https://i.postimg.cc/MTw0LnTV/logo-clinic.png';

// Email recipient
$recipient_email = 'pbon04@gmail.com';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mazktp@gmail.com';     // ✅ Replace with your Gmail
    $mail->Password   = 'pknn vtca losh fenj';         // ✅ App password here
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('mazktp@gmail.com', 'Ansar Hospital');
    $mail->addAddress($recipient_email);

    $mail->isHTML(true);
    $mail->Subject = 'Appointment Confirmation - Ansar Hospital';

    $mail->Body = "
    <div style='border:1px solid #ccc; padding:20px; font-family:sans-serif;'>
        <div style='text-align:center;'>
            <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                    <td align='center'>
                        <img src='$logo_url' height='80' style='display:block; margin:0 auto;' alt='Clinic Logo'>
                    </td>
                </tr>
            </table>

            <h2 style='color:#007bff; margin-top:10px;'>Ansar Hospital</h2>
            <h4 style='color:#28a745;'>Appointment Confirmation</h4>
        </div>

        <table style='width:100%; border-collapse: collapse; margin-top:20px;' border='1'>
            <thead>
                <tr style='background-color:#007bff; color:#fff;'>
                    <th style='padding:8px;'>Field</th>
                    <th style='padding:8px;'>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style='padding:8px;'>Patient ID</td><td style='padding:8px;'>$patient_id</td></tr>
                <tr><td style='padding:8px;'>Name</td><td style='padding:8px;'>$prefix $name</td></tr>
                <tr><td style='padding:8px;'>H/F Name</td><td style='padding:8px;'>$title $hf_name</td></tr>
                <tr><td style='padding:8px;'>Address</td><td style='padding:8px;'>$address, $city</td></tr>
                <tr><td style='padding:8px;'>Mobile</td><td style='padding:8px;'>$mobile</td></tr>
                <tr><td style='padding:8px;'>Doctor</td><td style='padding:8px;'>$doctor_name</td></tr>
                <tr><td style='padding:8px;'>Appointment Time</td><td style='padding:8px;'>$appointment_time</td></tr>
            </tbody>
        </table>

        <p style='margin-top:20px; font-size:14px; color:#555;'>This is an automated message. Thank you for choosing <strong>Ansar Hospital</strong>.</p>
    </div>
    ";

    $mail->send();
    echo '✅ Appointment email sent successfully.';
} catch (Exception $e) {
    echo "❌ Email sending failed. Error: {$mail->ErrorInfo}";
}
?>
