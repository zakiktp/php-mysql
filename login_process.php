<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_id = trim($_POST['login_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($login_id && $password) {
        $stmt = $pdo->prepare("
            SELECT * FROM staff 
            WHERE staff_name = :id OR username = :id OR mobile = :id OR role = :id OR id = :id
        ");
        $stmt->execute(['id' => $login_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $_SESSION['login_error'] = "❌ No matching user found for '$login_id'";
        } elseif (!password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = "❌ Password mismatch for '$login_id'";
        } else {
            // Login successful
            $_SESSION['staff_name'] = $user['staff_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['staff_id'] = $user['id'];

            header("Location: dashboard.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "❌ Please enter login ID and password.";
    }

    header("Location: login.php");
    exit;
}
?>
