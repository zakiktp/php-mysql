<?php
session_start();
require_once __DIR__ . '/includes/db_connection.php';

// Capture form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Username and Password are required.";
    header("Location: login.php");
    exit;
}

try {
    // ✅ Match against 'staff' table, not 'users'
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // ✅ Login success
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['staff_name'] = $user['staff_name'];

        header("Location: dashboard.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid username or password.";
        header("Location: dashboard.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['login_error'] = "Database error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}
