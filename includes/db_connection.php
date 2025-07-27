<?php
$host = '127.0.0.1';       // or 'localhost'
$port = '3306';            // ✅ Your MySQL port
$dbname = 'hospital_db';   // ✅ Confirm in phpMyAdmin
$username = 'root';        // Default in XAMPP
$password = '';            // Default in XAMPP

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connected to DB: $dbname";
} catch (PDOException $e) {
    echo "❌ Local DB connection failed: " . $e->getMessage();
    exit;
}
?>
