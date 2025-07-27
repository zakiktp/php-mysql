<?php
// Database connection
$host = "localhost";
$port = 3307;
$user = "root";
$password = ""; // update if your MySQL has a password
$database = "hospital_db";

$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// User data
$users = [
    [
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'email' => 'admin@hospital.com',
        'full_name' => 'System Administrator',
        'role' => 'admin',
        'status' => 'active'
    ],
    [
        'username' => 'staff1',
        'password' => password_hash('staff123', PASSWORD_DEFAULT),
        'email' => 'staff1@hospital.com',
        'full_name' => 'Reception Staff',
        'role' => 'receptionist',
        'status' => 'active'
    ]
];

// Insert users
foreach ($users as $user) {
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $user['username']);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user['username'], $user['password'], $user['email'], $user['full_name'], $user['role'], $user['status']);
        $stmt->execute();
        echo "✅ Inserted user: " . $user['username'] . "<br>";
    } else {
        echo "ℹ️ User already exists: " . $user['username'] . "<br>";
    }
    $check->close();
}

$conn->close();
?>
