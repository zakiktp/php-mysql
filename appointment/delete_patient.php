<?php
require_once('../includes/db_connection.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
$stmt->execute([$id]);

header("Location: appointment/patients_list.php");
exit;
?>
