<?php
session_start();
require 'db_connection.php';

// Only allow admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

if (!isset($_GET['id'])) {
    echo "No staff ID provided.";
    exit;
}

$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch();

if (!$staff) {
    echo "Staff not found.";
    exit;
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_name = strtoupper(trim($_POST['staff_name']));
    $username = strtolower(trim($_POST['username']));
    $mobile = trim($_POST['mobile']);
    $role = trim($_POST['role']);

    $update = $pdo->prepare("UPDATE staff SET staff_name=?, username=?, mobile=?, role=? WHERE id=?");
    $update->execute([$staff_name, $username, $mobile, $role, $id]);

    header("Location: staff_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <style>
        body { font-family: Arial; background: #f0f2f5; padding: 30px; }
        form {
            background: white; padding: 20px; max-width: 500px; margin: auto;
            border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; }
        input, select {
            width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background: #007BFF; color: white; padding: 10px 15px;
            border: none; border-radius: 4px; cursor: pointer; font-weight: bold;
        }
        a.exit-btn {
            display: block; text-align: center; margin-top: 20px;
            background: #6c757d; color: white; padding: 10px;
            border-radius: 5px; text-decoration: none;
        }
    </style>
</head>
<body>

<h2>✏️ Edit Staff</h2>

<form method="POST">
    <label>Staff Name:</label>
    <input type="text" name="staff_name" value="<?= htmlspecialchars($staff['staff_name']) ?>" required>

    <label>Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($staff['username']) ?>" required>

    <label>Mobile:</label>
    <input type="text" name="mobile" value="<?= htmlspecialchars($staff['mobile']) ?>">

    <label>Role:</label>
    <select name="role" required>
        <option value="admin" <?= $staff['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="staff" <?= $staff['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
    </select>

    <button type="submit">Update Staff</button>
</form>

<a href="staff_list.php" class="exit-btn">⬅ Back to Staff List</a>

</body>
</html>
