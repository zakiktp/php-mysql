<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = strtoupper(trim($_POST['address'] ?? ''));

    if ($address === '') {
        echo json_encode(['status' => 'error', 'message' => 'Empty address']);
        exit;
    }

    // Check if address already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM dropdownlist WHERE address = ?");
    $stmt->execute([$address]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        // Insert into dropdownlist
        $insert = $pdo->prepare("INSERT INTO dropdownlist (address) VALUES (?)");
        $insert->execute([$address]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Address saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
