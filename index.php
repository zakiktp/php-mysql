<?php
session_start();
require 'db_connection.php';

function calculateAgeFromDOB($dob) {
    if (!$dob || $dob === '0000-00-00') return '';
    $dobDate = new DateTime($dob);
    $now = new DateTime();
    $diff = $dobDate->diff($now);
    return $diff->y . 'y/' . $diff->m . 'm/' . $diff->d . 'd';
}

$date_today = date('Y-m-d');
$filter = $_GET['filter'] ?? 'today';

if ($filter === 'all') {
    $query = "SELECT a.*, p.dob FROM appointments a
              LEFT JOIN patients p ON a.patient_id = p.patient_id
              ORDER BY a.entry_datetime DESC";
    $stmt = $pdo->query($query);
} else {
    $query = "SELECT a.*, p.dob FROM appointments a
              LEFT JOIN patients p ON a.patient_id = p.patient_id
              WHERE DATE(a.entry_datetime) = :date_today
              ORDER BY a.entry_datetime DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['date_today' => $date_today]);
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
    </style>
</head>
<body>

<div class="container mt-3">
    <div class="action-bar">
        <div class="left">
            <h4>Appointments <?= $filter === 'today' ? "(Today: $total_today)" : "" ?></h4>
        </div>
        <div class="right">
            <a href="?filter=today" class="btn btn-sm btn-primary <?= $filter === 'today' ? 'active' : '' ?>">Today's Appointments</a>
            <a href="?filter=all" class="btn btn-sm btn-secondary <?= $filter === 'all' ? 'active' : '' ?>">All Appointments</a>
            <a href="patients_list.php" class="btn btn-sm btn-success">Patient List</a>
            <a href="new_appointment.php" class="btn btn-sm btn-outline-primary">➕ New Appointment</a>
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
                    <td><?= htmlspecialchars($row['doctor_name'] ?? '') ?></td>
                    <td><?= isset($row['entry_datetime']) ? date('d-m-Y h:i A', strtotime($row['entry_datetime'])) : '' ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['staff'] ?? '') ?></td>
                    <td>
                        <a href="edit_appointment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="view_appointment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
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
        dom: 'Bfrtip',
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: 'Export Excel' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', orientation: 'landscape', pageSize: 'A4', text: 'Export PDF' },
            { extend: 'print', className: 'btn btn-primary btn-sm', text: 'Print' }
        ],
        fixedHeader: true
    });
});
</script>
</body>
</html>
