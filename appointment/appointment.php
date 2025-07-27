<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /hospital_php/login.php");
    exit;
}

include("../includes/header.php");

// Define submenus for Appointment module
$modules = [
    "Appointment" => ["New Appointment", "Appointment List"]
];
?>

<main class="container-fluid">
    <div class="row">
        <!-- Sidebar Submenu -->
        <aside class="col-md-2 bg-primary text-white p-3" style="min-height: 100vh;">
            <h6 class="text-white">Appointment Menu</h6>
            <ul class="nav flex-column" id="subMenuContainer">
                <li class="nav-item">
                    <a href="/appointment/new_appointment.php" class="nav-link text-white">📝 New Appointment</a>
                </li>
                <li class="nav-item">
                    <a href="/appointment/appointment_list.php" class="nav-link text-white">📋 Appointment List</a>
                </li>
            </ul>
        </aside>

        <!-- Main content -->
        <section class="col-md-10 dashboard-content">
            <h3>Appointment Module</h3>
            <p class="text-muted">You can register new appointments or manage existing ones from here.</p>
            
            <!-- Example placeholder -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">Upcoming Appointments</div>
                <div class="card-body">
                    <p>Here you'd show the list of appointments or summaries...</p>
                </div>
            </div>
        </section>
    </div>
</main>

<?php include("../includes/footer.php"); ?>
