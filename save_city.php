<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = strtoupper(trim($_POST['city'] ?? ''));

    if ($city === '') {
        echo json_encode(['status' => 'error', 'message' => 'Empty city']);
        exit;
    }

    // Check if city already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM dropdownlist WHERE city = ?");
    $stmt->execute([$city]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        // Insert into dropdownlist
        $insert = $pdo->prepare("INSERT INTO dropdownlist (city) VALUES (?)");
        $insert->execute([$city]);
    }

    echo json_encode(['status' => 'success', 'message' => 'City saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
