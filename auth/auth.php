<?php
session_start();
require_once('../includes/db.php');

$username = $_POST['username'];
$password = $_POST['password'];

// Search by username only
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify hashed password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];

        // ✅ Get staff details using correct relationship
        $staff_stmt = $conn->prepare("
            SELECT s.id, s.staff_name 
            FROM staff s
            JOIN users u ON u.staff_id = s.id
            WHERE u.username = ?
            LIMIT 1
        ");
        $staff_stmt->bind_param("s", $user['username']);
        $staff_stmt->execute();
        $staff_result = $staff_stmt->get_result();

        if ($staff_result->num_rows === 1) {
            $staff = $staff_result->fetch_assoc();
            $_SESSION['staff_id'] = $staff['id'];
            $_SESSION['full_name'] = $staff['staff_name'];
        } else {
            $_SESSION['staff_id'] = 0;
            $_SESSION['full_name'] = '';
        }


        header("Location: ../dashboard.php");
        exit;
    } else {
        header("Location: ../login.php?error=Invalid password");
        exit;
    }
} else {
    header("Location: ../login.php?error=User not found");
    exit;
}
?>
