<?php
session_start();
require 'db_connection.php';

// Restrict to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit;
}

$stmt = $pdo->query("SELECT * FROM staff ORDER BY id DESC");
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6fa;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px 14px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }
        .edit {
            background-color: #ffc107;
            color: black;
        }
        .delete {
            background-color: #dc3545;
            color: white;
        }
        .add {
            display: block;
            width: 160px;
            margin: 20px auto;
            background-color: #28a745;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .exit {
            display: block;
            width: 200px;
            margin: 20px auto;
            background-color: #6c757d;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .exit:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

    <h2>👥 Staff Management</h2>

    <a href="register_staff.php" class="add">➕ Add New Staff</a>

    <table>
        <tr>
            <th>ID</th>
            <th>Staff Name</th>
            <th>Username</th>
            <th>Mobile</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($staffList as $staff): ?>
        <tr>
            <td><?= htmlspecialchars($staff['id']) ?></td>
            <td><?= htmlspecialchars($staff['staff_name']) ?></td>
            <td><?= htmlspecialchars($staff['username']) ?></td>
            <td><?= htmlspecialchars($staff['mobile']) ?></td>
            <td><?= htmlspecialchars($staff['role']) ?></td>
            <td>
                <a href="edit_staff.php?id=<?= $staff['id'] ?>" class="btn edit">Edit</a>
                <a href="delete_staff.php?id=<?= $staff['id'] ?>" class="btn delete" onclick="return confirm('Delete this staff?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <a href="dashboard.php" class="exit">⬅ Exit to Dashboard</a>

</body>
</html>
