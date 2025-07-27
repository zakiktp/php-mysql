<?php
require_once('../includes/db_connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

date_default_timezone_set('Asia/Kolkata');

// Get and sanitize the limit value from GET
$limit = $_GET['limit'] ?? 25;
$limit = ($limit === 'all') ? 'all' : (int)$limit;


// ✅ Define all used variables early
$filter = $_GET['filter'] ?? '';
$today = date('Y-m-d');         // This is used correctly
$date_today = date('Y-m-d');    // ✅ Fix: use MySQL-compatible format

// ✅ Query to fetch today's appointments
// No need for PHP-based date calculation
$stmt = $pdo->query("SELECT * FROM appointments WHERE DATE(entry_datetime) = CURDATE()");
$appointments = $stmt->fetchAll();

function calculateAgeFromDOB($dob) {
    if (!$dob || $dob === '0000-00-00') return '';
    $dobDate = new DateTime($dob);
    $now = new DateTime();
    $diff = $dobDate->diff($now);
    return $diff->y . 'y/' . $diff->m . 'm/' . $diff->d . 'd';
}

if ($filter === 'all') {
    $query = "SELECT a.*, p.dob, d.doctor_name AS doctor_name, s.staff_name AS staff_name
              FROM appointments a
              LEFT JOIN patients p ON a.patient_id = p.patient_id
              LEFT JOIN doctors d ON a.doctor_id = d.id
              LEFT JOIN staff s ON a.staff_id = s.id
              ORDER BY a.entry_datetime DESC";
    if ($limit !== 'all') {
        $query .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->query($query);
        
} else {
    $query = "SELECT a.*, p.dob, d.doctor_name AS doctor_name, s.staff_name AS staff_name
              FROM appointments a
              LEFT JOIN patients p ON a.patient_id = p.patient_id
              LEFT JOIN doctors d ON a.doctor_id = d.id
              LEFT JOIN staff s ON a.staff_id = s.id
              WHERE DATE(a.entry_datetime) = :date_today
              ORDER BY a.entry_datetime DESC";

    // Apply limit if not 'all'
    if ($limit !== 'all') {
        $query .= " LIMIT :limit";
    }

    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':date_today', $date_today);
    if ($limit !== 'all') {
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    }
    $stmt->execute();
}

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count_today = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE DATE(entry_datetime) = ?");
$count_today->execute([$date_today]);
$total_today = $count_today->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointments List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-size: 0.85rem;
        }
        .action-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .action-bar .left,
        .action-bar .right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .table th, .table td {
            font-size: 0.85rem;
            vertical-align: middle;
            padding: 6px 10px;
        }
        .btn-sm {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        h4 {
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Enhanced table styles for local visibility */
    #appointmentsTable th, #appointmentsTable td {
        border: 1px solid #555 !important;
        color: #003366 !important; /* Dark blue font */
        vertical-align: middle;
        font-size: 14px;
    }

    #appointmentsTable th {
        background-color: #1a1a1a !important;
        color: white !important;
        font-weight: bold;
    }

    #appointmentsTable tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #appointmentsTable {
        border-collapse: collapse !important;
    }
    @import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

    .dropdown-menu {
        min-width: 120px;
        font-size: 0.85rem;
    }
    .btn.active {
        font-weight: bold;
        border: 2px solid #0d6efd !important;
        background-color: #0b5ed7 !important;
        color: #fff !important;
    }


    </style>
</head>
<body>
<div class="alert alert-info mb-3">
    <strong>📅 Today's Appointments:</strong> <?= $total_today ?>
</div>

<div class="container mt-3">
    <div class="action-bar d-flex justify-content-between align-items-center">
        <div class="left">
            <h4 class="mb-0">Appointments <?= $filter === 'today' ? "(Today: $total_today)" : "" ?></h4>
        </div>
        <div class="right d-flex align-items-center gap-2">

            <!-- Record Limit Dropdown -->
            <form method="get" class="d-inline me-2">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <select name="limit" onchange="this.form.submit()" class="form-select form-select-sm w-auto">
                    <?php
                    $options = [10, 25, 50, 100, 'all'];
                    $selected_limit = $_GET['limit'] ?? 25;
                    foreach ($options as $opt) {
                        $label = ($opt === 'all') ? 'All' : $opt;
                        $sel = ($opt == $selected_limit) ? 'selected' : '';
                        echo "<option value='$opt' $sel>$label</option>";
                    }
                    ?>
                </select>
            </form>

            <!-- Action Buttons -->
            <a href="?filter=today" class="btn btn-sm btn-primary <?= $filter === 'today' ? 'active' : '' ?>">Today's Appointments</a>
            <a href="?filter=all" class="btn btn-sm btn-secondary <?= $filter === 'all' ? 'active' : '' ?>">All Appointments</a>
            <a href="patient_list.php" class="btn btn-sm btn-success">Patient List</a>
            <a href="new.php" class="btn btn-sm btn-outline-primary">➕ New Appointment</a>
            <a href="/hospital_php/dashboard.php" class="btn btn-sm btn-dark">← Exit to Dashboard</a>
        </div>
    </div>
</div>


    <table id="appointmentsTable" class="table table-bordered table-hover bg-white">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Patient ID</th>
                <th>Prefix</th>
                <th>Name</th>
                <th>Title</th>
                <th>H/F Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>DOB</th>
                <th>Mobile</th>
                <th>Doctor</th>
                <th>Entry Date/Time</th>
                <th>Status</th>
                <th>Staff</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($appointments): ?>
            <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['patient_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['prefix'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['title'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['hf_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['gender'] ?? '') ?></td>
                    <td><?= calculateAgeFromDOB($row['dob'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['dob'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['mobile'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['doctor_name'] ?? 'N/A') ?></td>
                    <td><?= !empty($row['entry_datetime']) ? date('d-m-Y h:i A', strtotime($row['entry_datetime'])) : '' ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['staff_name'] ?? 'N/A') ?></td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Actions
                            </button>
                            <ul class="dropdown-menu animate__animated animate__fadeIn">
                                <li><a class="dropdown-item text-warning" href="/hospital_php/appointment/edit_appointment.php?id=<?= $row['id'] ?>">✏️ Edit</a></li>
                                <li><a class="dropdown-item text-info" href="/hospital_php/appointment/view_appointment.php?id=<?= $row['id'] ?>">👁️ View</a></li>
                                <!-- Optional Delete -->
                                <!-- <li><a class="dropdown-item text-danger" href="/hospital_php/appointment/delete_appointment.php?id=<?= $row['id'] ?>">🗑️ Delete</a></li> -->
                            </ul>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="15" class="text-center">No appointments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function () {
    $('#appointmentsTable').DataTable({
        responsive: true,
        fixedHeader: true,
        dom: 'Bfrtip',
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, 'All']
        ],
        pageLength: 25,
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm', text: 'Copy' },
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: 'Export Excel' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', orientation: 'landscape', pageSize: 'A4', text: 'Export PDF' },
            { extend: 'print', className: 'btn btn-primary btn-sm', text: 'Print' }
        ]
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
