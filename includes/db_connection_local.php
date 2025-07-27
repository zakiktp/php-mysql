<?php
$host = '127.0.0.1';
$port = '3307'; // Ensure XAMPP MySQL uses 3307
$dbname = 'users_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Local DB connected successfully!";
} catch (PDOException $e) {
    die("❌ Local DB connection failed: " . $e->getMessage());
}
?>
