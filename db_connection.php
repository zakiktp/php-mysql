<?php
$host = '127.0.0.1';
$port = '3307'; // ✅ Updated port
$dbname = 'users_db'; // change to your actual DB name
$username = 'root';
$password = ''; // update if you’ve set a password

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connected successfully!";
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
