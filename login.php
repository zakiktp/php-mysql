<?php
session_start();
require 'db_connection.php';

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']); // Clear error
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff Login</title>
    <style>
        body {
            background-color: #eef2f7;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #2a9df4;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1d80d0;
        }
        .error {
            color: red;
            background-color: #ffe5e5;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .toggle-pass {
            position: relative;
        }
        .toggle-icon {
            position: absolute;
            right: 15px;
            top: 45%;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>🔐 Staff Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="login_process.php">
        <label><strong>Login ID:</strong></label>
        <input type="text" name="login_id" required>

        <label><strong>Password:</strong></label>
        <div class="toggle-pass">
            <input type="password" name="password" id="password" required>
            <span class="toggle-icon" onclick="togglePassword()">👁️</span>
        </div>
        <button type="submit" class="btn">Login</button>
        <div style="text-align: right; margin-top: 10px;">
            <a href="forgot_password.php" style="color: #007bff; text-decoration: none;">Forgot Password?</a>
        </div>
    </form>

</div>

<script>
function togglePassword() {
    const passInput = document.getElementById("password");
    passInput.type = passInput.type === "password" ? "text" : "password";
}
</script>
</body>
</html>


