<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['module'])) {
    $_SESSION['selected_module'] = $_POST['module'];
    http_response_code(200);
    echo 'Module set';
} else {
    http_response_code(400);
    echo 'Invalid request';
}
