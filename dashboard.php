<?php
session_start();
if (!isset($_SESSION['staff_name'])) {
    header("Location: login.php");
    exit();
}

$staff_name = $_SESSION['staff_name'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Al-Hayat Clinic Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f7fb;
  }

  .header {
    background-color: #004466;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
  }

  .clinic-title {
    font-size: 24px;
    font-weight: bold;
  }

  .clinic-subtitle {
    font-size: 14px;
  }

  .user-info {
    text-align: right;
  }

  .logout-btn {
    background-color: #ff4d4d;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    margin-top: 5px;
  }

  .container {
    display: flex;
    min-height: calc(100vh - 160px); /* Adjust for header + doctor bar height */
  }

  .sidebar {
    width: 250px;
    background-color: #2f4050;
    color: white;
    padding: 15px;
    flex-shrink: 0;
  }

  .sidebar img {
    max-width: 120px;
    height: auto;
    margin-bottom: 10px;
  }

  .sidebar button,
  .sidebar a {
    display: block;
    width: 100%;
    text-align: left;
    padding: 10px;
    background: none;
    border: none;
    color: white;
    font-size: 15px;
    cursor: pointer;
    text-decoration: none;
  }

  .sidebar button:hover,
  .sidebar a:hover {
    background-color: #1c2733;
  }

  .submenu,
  .dropdown-container {
    display: none;
    padding-left: 15px;
  }

  .doctor-bar {
    display: flex;
    flex-wrap: wrap;
    background-color: #ffffff;
    border-bottom: 2px solid #ccc;
    padding: 10px;
    justify-content: space-around;
  }

  .doctor-card {
    text-align: center;
    margin: 5px 10px;
    line-height: 1.4;
  }

  iframe {
    flex-grow: 1;
    border: none;
    width: 100%;
    height: calc(100vh - 160px); /* adjust based on header + doctor bar */
  }

  .fa-caret-down {
    float: right;
  }
</style>

<!-- HEADER -->
<div class="header">
  <div>
    <div class="clinic-title">ANSAR HOSPITAL</div>
    <div class="clinic-subtitle">Mahajanan, Kiratpur | Ph: 9219568512</div>
  </div>
  <div class="user-info">
    <div><strong>User:</strong> <?= htmlspecialchars($staff_name) ?></div>
    <div><strong>Role:</strong> <?= htmlspecialchars($role) ?></div>
    <form method="post" action="logout.php">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>
</div>

<!-- DOCTOR INFO BAR -->
<div class="doctor-bar">
  <div class="doctor-card">
    <b>Dr. M. Zaki Ansari</b><br>
    <span>B.U.M.S. (Aligarh)</span><br>
    <span>Regd. No. 6442</span>
  </div>
  <div class="doctor-card">
    <b>Dr. Mrs. Nishat Ansari</b><br>
    <span>B.U.M.S. (Mumbai)</span><br>
    <span>Regd. No. 6862</span>
  </div>
  <div class="doctor-card">
    <b>Dr. M. Affan Zaki Ansari</b><br>
    <span>M.B.B.S., M.D. (Std.)</span><br>
    <span>Regd. No. 92441</span>
  </div>
  <div class="doctor-card">
    <b>Dr. Shanaz</b><br>
    <span>M.B.B.S., M.D. (Std.)</span><br>
    <span>Regd. No. 112440</span>
  </div>
</div>

<!-- MAIN LAYOUT -->
<div class="container">
  <!-- SIDEBAR -->
  <div class="sidebar">
    <img src="logo_clinic.png" alt="Clinic Logo">

    <!-- Appointments Dropdown -->
    <button onclick="toggleDropdown('appointmentMenu')">
      <i class="fa-solid fa-calendar-plus"></i> Appointments
      <i class="fa fa-caret-down" style="float:right;"></i>
    </button>
    <div id="appointmentMenu" class="submenu" style="display:none;">
      <a href="javascript:void(0);" onclick="loadPage('new_appointment.php')">
        <i class="fa fa-calendar-plus"></i> New Appointment
      </a>
      <a href="javascript:void(0);" onclick="loadPage('index.php')">
        <i class="fa fa-list"></i> All Appointments
      </a>
    </div>


    <!-- Attendance -->
    <button onclick="window.location.href='attendance_module/index.php'">
      <i class="fa-solid fa-user-check"></i> Attendance Module
    </button>

    <!-- OPD -->
    <button onclick="window.location.href='opd/index.php'">
      <i class="fa-solid fa-hospital-user"></i> OPD
    </button>

    <!-- Discharge -->
    <button onclick="window.location.href='discharge/index.php'">
      <i class="fa-solid fa-file-medical"></i> Discharge
    </button>

    <!-- Pharmacy -->
    <button onclick="window.location.href='pharmacy/index.php'">
      <i class="fa-solid fa-pills"></i> Pharmacy
    </button>

    <!-- Staff Management -->
    <button onclick="toggleDropdown('staffMenu')">
      <i class="fa-solid fa-user-gear"></i> Staff Management
      <i class="fa fa-caret-down"></i>
    </button>
    <div id="staffMenu" class="dropdown-container">
      <a href="javascript:void(0);" onclick="loadPage('register_staff.php')">
        <i class="fa fa-user-plus"></i> Register Staff
      </a>
      <a href="javascript:void(0);" onclick="loadPage('staff_list.php')">
        <i class="fa fa-users"></i> Staff List
      </a>
      <a href="javascript:void(0);" onclick="loadPage('project_master_index.php')">
        <i class="fa fa-sitemap"></i> Project Master Index
      </a>
    </div>
  </div>

  <!-- MAIN CONTENT IFRAME -->
  <iframe id="mainFrame" src=""></iframe>
</div>

<!-- JS SCRIPT -->
<script>
  function loadPage(url) {
    document.getElementById('mainFrame').src = url;

    // Optionally close all dropdowns
    document.querySelectorAll('.submenu, .dropdown-container').forEach(el => el.style.display = 'none');
  }

  function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const isVisible = dropdown.style.display === "block";

    // Hide all first
    document.querySelectorAll('.submenu, .dropdown-container').forEach(el => el.style.display = 'none');

    // Toggle clicked one
    if (!isVisible) {
      dropdown.style.display = "block";
    }
  }

  document.addEventListener('click', function (event) {
    if (!event.target.closest('.sidebar button') &&
        !event.target.closest('.submenu') &&
        !event.target.closest('.dropdown-container')) {
      document.querySelectorAll('.submenu, .dropdown-container').forEach(el => el.style.display = 'none');
    }
  });
</script>


</body>
</html>
