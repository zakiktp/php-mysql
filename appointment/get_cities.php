<?php
require_once('../includes/db.php');
header('Content-Type: application/json');

// Fetch non-empty cities, sorted A-Z
$stmt = $pdo->prepare("SELECT DISTINCT city FROM dropdownlist WHERE city IS NOT NULL AND city != '' ORDER BY city ASC");
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($cities);
?>
