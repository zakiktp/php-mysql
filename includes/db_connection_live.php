<?php
$host = 'sql112.infinityfree.com';
$dbname = 'if0_39544740_appointment';
$username = 'if0_39544740';
$password = 'Bq6divpSJem7Lrk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Live DB connected successfully!";
} catch (PDOException $e) {
    die("❌ Live DB connection failed: " . $e->getMessage());
}
?>
