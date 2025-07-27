<?php
session_start();

// Set idle timeout duration (in seconds)
$timeout_duration = 900; // 15 minutes

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check for last activity timestamp
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Inactivity timeout reached – destroy session
    session_unset();
    session_destroy();
    header("Location: login.php?error=Session expired due to inactivity");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();
