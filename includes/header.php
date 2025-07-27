<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$selected_module = $_SESSION['selected_module'] ?? null;

$modules = [
  'Appointment' => 'fa-calendar-check',
  'OPD' => 'fa-stethoscope',
  'Pharmacy' => 'fa-pills',
  'Lab' => 'fa-vials',
  'Procedures' => 'fa-syringe',
  'IPD' => 'fa-bed',
  'Staff' => 'fa-user-md',
  'Financials' => 'fa-file-invoice-dollar',
  'Project' => 'fa-chart-line'
];

$submenus = [
  'Appointment' => ['New', 'List', 'Follow-up', 'Patient List'],
  'OPD' => ['Consultation', 'Follow-up'],
  'Pharmacy' => ['Inventory', 'Sales', 'Purchases'],
  'Lab' => ['Tests', 'Results'],
  'Procedures' => ['Schedule', 'Completed'],
  'IPD' => ['Admissions', 'Discharge'],
  'Staff' => ['Staff List', 'Register'],
  'Financials' => ['Billing', 'Payments'],
  'Project' => ['Reports', 'Analytics']
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ANSAR Hospital</title>

  <!-- Bootstrap, FontAwesome, Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />

  <style>
    .module-grid {
      display: grid;
      grid-template-columns: repeat(3, 100px);
      gap: 15px;
      padding: 10px;
    }

    .module-icon {
      text-align: center;
      cursor: pointer;
      padding: 10px;
      border-radius: 8px;
      background-color: #f8f9fa;
      transition: 0.3s;
    }

    .module-icon:hover {
      background-color: #e2e6ea;
    }

    .module-link.active {
      background-color: #0d6efd;
      color: #fff;
      font-weight: bold;
    }

    .navbar-nav .nav-link:hover {
      text-decoration: underline;
    }
    .module-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 10px;
  }

  .module-icon {
    text-align: center;
    cursor: pointer;
    padding: 15px;
    border-radius: 10px;
    background-color: #ffffff;
    transition: 0.3s;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
  }

  .module-icon:hover {
    background-color: #e9f2fc;
    transform: translateY(-3px);
  }

  .module-icon i {
    color: #0d6efd;
  }
  .module-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Already correct */
    gap: 15px;
    padding: 10px;
  }
  .navbar-nav {
    max-height: 0vh;       /* Adjust as needed */
    overflow-y: auto;
  }
  .module-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 10px;
  }
  .active-submenu {
      background-color: #ffffff;
      color: #0d6efd !important;
      font-weight: bold;
      border-radius: 5px;
      padding: 5px 10px;
    }

  </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
  <!-- Logo -->
  <a class="navbar-brand d-flex align-items-center" href="/hospital_php/dashboard.php">
    <img src="/hospital_php/assets/images/logo_clinic.png" alt="Logo" height="40" class="me-2" />
    <span class="fw-bold text-white">ANSAR Hospital</span>
  </a>

  <!-- Spacer to push rest to the right -->
  <div class="ms-3 dropdown">
    <button class="btn btn-outline-light" id="moduleDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fas fa-th-large"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-start p-3" style="min-width: 330px; max-height: 450px; overflow-y: auto;">
      <div class="module-grid">
        <?php foreach ($modules as $module => $icon): ?>
          <div class="module-icon" onclick="selectModule('<?php echo $module; ?>')">
            <i class="fas <?php echo $icon; ?> fa-2x mb-1"></i>
            <div style="font-size: 0.9rem;"><?php echo $module; ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <?php if ($selected_module && isset($submenus[$selected_module])): ?>
  <div class="bg-primary px-3 py-2 submenu-bar">
    <ul class="nav">
      <?php foreach ($submenus[$selected_module] as $submenu): ?>
        <?php
          $file = strtolower(str_replace(' ', '_', $submenu)) . '.php';
          $module_folder = strtolower($selected_module);
          $url = "/hospital_php/{$module_folder}/{$file}";
          $isActive = strpos($_SERVER['REQUEST_URI'], $file) !== false;
        ?>
        <li class="nav-item me-3">
          <a class="nav-link <?= $isActive ? 'active-submenu' : 'text-white' ?>" href="<?= $url ?>">
            <?= $submenu ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

  <!-- Right-aligned user info + logout -->
  <div class="ms-auto d-flex align-items-center">
    <span class="text-white me-3 fw-semibold"><?= $_SESSION['username'] ?? '' ?></span>
    <a class="btn btn-sm btn-outline-light" href="/hospital_php/auth/logout.php" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
    </a>
  </div>
</nav>

<script>
  function selectModule(moduleName) {
    fetch("/hospital_php/set_module.php", {
      method: "POST",
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'module=' + encodeURIComponent(moduleName)
    }).then(() => location.reload());
  }
</script>
