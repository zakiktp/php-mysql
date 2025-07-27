<?php
session_start();
require_once("../includes/db_connection.php");

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

// Confirm deletion
$stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$id]);
$staff = $stmt->fetch();

if (!$staff) {
    echo "Staff not found.";
    exit;
}

// Delete logic
if (isset($_POST['confirm'])) {
    $del = $pdo->prepare("DELETE FROM staff WHERE id = ?");
    $del->execute([$id]);

    header("Location: staff_list.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Staff</title>
    <style>
        body { font-family: Arial; background: #fef1f1; padding: 30px; }
        .box {
            background: white; padding: 30px; max-width: 500px; margin: auto;
            border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 { color: #dc3545; }
        form { margin-top: 20px; }
        button {
            padding: 10px 20px; margin: 5px;
            border: none; border-radius: 4px; font-weight: bold; cursor: pointer;
        }
        .confirm { background: #dc3545; color: white; }
        .cancel { background: #6c757d; color: white; }
    </style>
</head>
<body>

<div class="box">
    <h2>⚠️ Confirm Deletion</h2>
    <p>Are you sure you want to delete <strong><?= htmlspecialchars($staff['staff_name']) ?></strong>?</p>

    <form method="POST">
        <button type="submit" name="confirm" class="confirm">Yes, Delete</button>
        <a href="staff_list.php"><button type="button" class="cancel">Cancel</button></a>
    </form>
</div>

</body>
</html>
