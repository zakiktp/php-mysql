<?php
session_start();
require_once("../includes/db_connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_name = $_POST['staff_name'] ?? '';
    $mobile = $_POST['mobile'] ?? '';
    $designation = $_POST['designation'] ?? '';
    $department = $_POST['department'] ?? '';
    $user_id = $_SESSION['user_id'];

    if ($staff_name && $mobile) {
        try {
            $stmt = $pdo->prepare("INSERT INTO staff (staff_name, mobile, designation, department, user_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$staff_name, $mobile, $designation, $department, $user_id]);
            $success = "Staff registered successfully.";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

include("../includes/header.php");
?>

<main class="container py-4">
    <h2 class="mb-3">Register Staff</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="staff_name" class="form-label">Staff Name *</label>
            <input type="text" class="form-control" name="staff_name" id="staff_name" required>
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile</label>
            <input type="text" class="form-control" name="mobile" id="mobile">
        </div>

        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" class="form-control" name="designation" id="designation">
        </div>

        <div class="mb-3">
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" name="department" id="department">
        </div>

        <button type="submit" class="btn btn-primary">Register Staff</button>
    </form>
</main>

<?php include("../includes/footer.php"); ?>
