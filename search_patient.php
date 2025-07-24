<?php
require_once('db_connection.php');

$term = $_GET['term'] ?? '';
$sql = "SELECT * FROM patients 
        WHERE patient_id LIKE :term 
           OR name LIKE :term 
           OR hf_name LIKE :term 
           OR address LIKE :term 
           OR mobile LIKE :term 
        LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute(['term' => "%$term%"]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($patients as $patient) {
    echo "<div class='border p-2 mb-2 patient-result' data-patient='" . json_encode($patient) . "'>
            <strong>{$patient['patient_id']} - {$patient['name']}</strong><br>
            {$patient['hf_name']} | {$patient['address']} | {$patient['mobile']}
        </div>";
}
?>
