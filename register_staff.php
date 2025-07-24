<?php
session_start();
require 'db_connection.php';

// Only allow admin to register staff
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_name = strtoupper(trim($_POST['staff_name']));
    $username = strtolower(trim($_POST['username']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $mobile = trim($_POST['mobile']);

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $message = "❌ Username already exists.";
    } else {
        $insert = $pdo->prepare("INSERT INTO staff (staff_name, username, password, role, mobile) VALUES (?, ?, ?, ?, ?)");
        if ($insert->execute([$staff_name, $username, $password, $role, $mobile])) {
            $message = "✅ Staff registered successfully.";
        } else {
            $message = "❌ Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Staff</title>
    <style>
        body { font-family: Arial; background: #eef2f7; padding: 30px; }
        .container {
            background: white; padding: 25px; border-radius: 8px;
            max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        input, select {
            width: 100%; padding: 10px; margin: 10px 0;
            border: 1px solid #ccc; border-radius: 5px;
        }
        button {
            width: 100%; padding: 10px; background: #007BFF;
            color: white; border: none; border-radius: 5px; font-weight: bold;
        }
        .msg {
            text-align: center; font-weight: bold;
            color: #dc3545; margin-top: 10px;
        }
        a.exit-btn {
            display: block; text-align: center; margin-top: 20px;
            background: #6c757d; color: white; padding: 10px;
            border-radius: 5px; text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>👤 Register New Staff</h2>

    <form method="POST">
        <input type="text" name="staff_name" placeholder="Staff Name" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
        </select>
        <input type="text" name="mobile" placeholder="Mobile Number">

        <button type="submit">Register</button>
    </form>

    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <a href="staff_list.php" class="exit-btn">⬅ Back to Staff List</a>
</div>

</body>
</html>
