<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_name = trim($_POST['staff_name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($staff_name && $email) {
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_name = :staff_name AND email = :email");
        $stmt->execute(['staff_name' => $staff_name, 'email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ✅ Match found – future: send email/OTP/reset link
            $_SESSION['forgot_message'] = "✅ Match found. Please contact admin or wait for reset instructions.";
            // Example future step: store reset request in table or send email
        } else {
            $_SESSION['forgot_message'] = "❌ No matching staff record found.";
        }
    } else {
        $_SESSION['forgot_message'] = "❌ Both Staff Name and Email are required.";
    }
}

// Redirect back to the forgot password page
header("Location: forgot_password.php");
exit;
