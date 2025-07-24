<?php
require 'db_connection.php';
$stmt = $pdo->query("SELECT * FROM patients ORDER BY created_at DESC");
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Patient List</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
  <style>
    body {
      background: #f4f7fb;
      font-size: 0.9rem;
    }
    .container {
      margin-top: 30px;
    }
    table th, table td {
      vertical-align: middle !important;
    }
    h4 {
      font-weight: bold;
    }
    .text-uppercase {
      text-transform: uppercase;
    }

  </style>
</head>
<body>

<div class="container">
  <h4 class="mb-4 d-flex justify-content-between align-items-center">
    <span>👨‍⚕️ Patient List</span>
    <a href="dashboard.php" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
  </h4>

  <table id="patientsTable" class="table table-bordered table-striped table-hover">
    <thead class="table-primary">
      <tr>
        <th>Patient ID</th>
        <th>Name</th>
        <th>Title + F/H Name</th> <!-- New Column -->
        <th>Mobile</th>
        <th>Gender</th>
        <th>Address</th>
        <th>City</th>
        <th>Age</th>
        <th>DOB</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($patients as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['patient_id']) ?></td>
        <td><?= htmlspecialchars($p['prefix'] . ' ' . $p['name']) ?></td>
        <td><?= htmlspecialchars($p['title'] . ' ' . $p['hf_name']) ?></td>
        <td><?= htmlspecialchars($p['mobile']) ?></td>
        <td><?= htmlspecialchars($p['gender']) ?></td>
        <td><?= htmlspecialchars($p['address']) ?></td>
        <td><?= htmlspecialchars($p['city']) ?></td>
        <td>
          <?php
            if (!empty($p['dob']) && $p['dob'] !== '0000-00-00') {
              $dob = new DateTime($p['dob']);
              $today = new DateTime();
              $diff = $today->diff($dob);
              echo $diff->y . "Y " . $diff->m . "M " . $diff->d . "D";
            } else {
              echo "-";
            }
          ?>
        </td>
        <td><?= htmlspecialchars($p['dob']) ?></td>
        <td>
          <a href="edit_patient.php?id=<?= urlencode($p['patient_id']) ?>" class="btn btn-sm btn-warning">Edit</a>
          <a href="delete_patient.php?id=<?= urlencode($p['patient_id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this patient?')">Delete</a>
      </td>

      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
  $(document).ready(function () {
    $('#patientsTable').DataTable({
      responsive: true,
      pageLength: 25,
      dom: 'Bfrtip',
      buttons: ['copy', 'excel', 'pdf', 'print']
    });
  });
</script>

</body>
</html>
