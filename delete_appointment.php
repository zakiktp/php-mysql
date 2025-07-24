if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied.";
    exit();
}
