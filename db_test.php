<?php
$conn = new mysqli("localhost", "root", "", "hospital_db", 3307);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully!";
?>
