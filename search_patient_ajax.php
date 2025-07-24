<?php
require_once 'db_connection.php'; // Ensure this is correct

header('Content-Type: application/json');

$search = $_GET['query'] ?? '';

if (strlen($search) < 3) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT patient_id, prefix, title, name, hf_name, gender, dob, mobile, address, city
        FROM patients
        WHERE patient_id LIKE :term 
           OR name LIKE :term 
           OR hf_name LIKE :term 
           OR address LIKE :term 
           OR mobile LIKE :term 
        LIMIT 15";

$stmt = $pdo->prepare($sql);
$term = "%$search%";
$stmt->execute(['term' => $term]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
?>
