<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "❌ Access Denied. Admins only.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>📁 Project Master Index</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
        }

        .sidebar {
            height: 100vh;
            width: 280px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }

        .sidebar h3 {
            color: #fff;
            font-size: 18px;
            margin-top: 20px;
        }

        .sidebar a {
            display: block;
            padding: 8px 12px;
            color: #ddd;
            text-decoration: none;
            border-radius: 4px;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            flex-grow: 1;
            padding: 30px;
            background: #f8f9fa;
        }

        .section-title {
            font-size: 1.4rem;
            margin: 30px 0 10px;
            color: #007bff;
        }

        .status {
            font-size: 0.85rem;
        }

        input[type="text"] {
            margin-bottom: 15px;
        }

        .logout {
            margin-top: 30px;
            color: #ffc107 !important;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2><i class="fa fa-sitemap"></i> Master Index</h2>

    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search...">

    <h3><i class="fa fa-lock"></i> Authentication</h3>
    <a href="login.php">Login</a>
    <a href="register_staff.php">Register Staff</a>
    <a href="forgot_password.php">Forgot Password</a>
    <a href="logout.php">Logout</a>

    <h3><i class="fa fa-user-gear"></i> Staff</h3>
    <a href="staff_list.php">Staff List ✅</a>
    <a href="edit_staff.php">Edit Staff 🛠️</a>
    <a href="delete_staff.php">Delete Staff 🛠️</a>

    <h3><i class="fa fa-calendar-check"></i> Appointments</h3>
    <a href="appointment_php/index.php">Appointment List ✅</a>
    <a href="appointment_php/new_appointment.php">New Appointment ✅</a>
    <a href="appointment_php/edit_appointment.php">Edit Appointment ✅</a>
    <a href="appointment_php/view_appointment.php">View Appointment ✅</a>
    <a href="appointment_php/delete_appointment.php">Delete Appointment ✅</a>
    <a href="appointment_php/save_appointment.php">Save Appointment ✅</a>
    <a href="appointment_php/update_appointment.php">Update Appointment ✅</a>

    <h3><i class="fa fa-users"></i> Patients</h3>
    <a href="patients_list.php">Patient List ✅</a>
    <a href="appointment_php/search_patient.php">Search Modal ✅</a>

    <h3><i class="fa fa-hospital"></i> Modules</h3>
    <a href="opd.html">OPD 🛠️</a>
    <a href="#">Pharmacy ❌</a>

    <h3><i class="fa fa-tools"></i> Tools</h3>
    <a href="db.php">DB Config ✅</a>
    <a href="functions.php">Functions ✅</a>
    <a href="dropdownlist.php">Dropdown List ✅</a>

    <a href="dashboard.php" class="logout"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="main-content">
    <h1>📁 Project Master Index</h1>
    <p>Below is the categorized list of all project files and modules for easy navigation and management.</p>

    <div class="alert alert-info">
        <strong>Legend:</strong> ✅ Completed | 🛠️ In Progress | ❌ Pending
    </div>

    <p><strong>Logged in as:</strong> <?= $_SESSION['username'] ?> (<?= $_SESSION['role'] ?>)</p>
</div>

<script>
    const searchInput = document.getElementById("searchInput");
    searchInput.addEventListener("keyup", () => {
        let filter = searchInput.value.toLowerCase();
        document.querySelectorAll(".sidebar a").forEach(link => {
            if (link.textContent.toLowerCase().includes(filter)) {
                link.style.display = "block";
            } else {
                link.style.display = "none";
            }
        });
    });
</script>

</body>
</html>
