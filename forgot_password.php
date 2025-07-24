<?php
require 'db_connection.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $mobile = trim($_POST['mobile']);
    $new_password = $_POST['new_password'];

    // Validate user
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ? AND mobile = ?");
    $stmt->execute([$username, $mobile]);

    if ($stmt->rowCount() > 0) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE staff SET password = ? WHERE username = ? AND mobile = ?");
        $update->execute([$hashed, $username, $mobile]);
        $message = "✅ Password updated successfully.";
    } else {
        $message = "❌ Invalid username or mobile.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { background: #f4f4f4; font-family: Arial; padding: 30px; }
        .box { background: white; max-width: 400px; margin: auto; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; font-weight: bold; border: none; border-radius: 5px; }
        .msg { text-align: center; color: red; margin-top: 15px; }
    </style>
</head>
<body>

<div class="box">
    <h2>🔐 Forgot Password</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="mobile" placeholder="Mobile Number" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>
</div>

</body>
</html>
