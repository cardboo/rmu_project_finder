<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}
$username = $_SESSION['username'];

include "../datacon.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


// Total projects (exclude archived)
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects WHERE is_archived = 0 OR is_archived IS NULL");
$totalQuery->execute();
$totalProjects = $totalQuery->get_result()->fetch_assoc()['total'];

// Total departments
$deptQuery = $conn->prepare("SELECT COUNT(*) AS total FROM departments");
$deptQuery->execute();
$totalDepartments = $deptQuery->get_result()->fetch_assoc()['total'];

// Recent audit logs (last 20)
$auditQuery = $conn->prepare("SELECT username, action, details, ip_address, created_at FROM audit_log ORDER BY created_at DESC LIMIT 20");
$auditQuery->execute();
$auditResult = $auditQuery->get_result();
$auditLogs = $auditResult->fetch_all(MYSQLI_ASSOC);
?>


<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/custom-theme.css" />

</head>


<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.html" class="text-nowrap logo-img">
            <img src="../assets/images/logos/RMU_logo.png" width="180" height="60" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../dashboard" aria-expanded="false">
                <span>
                  <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <!-- <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">UI COMPONENTS</span>
            </li> -->
            <li class="sidebar-item">
              <a class="sidebar-link" href="../view_departments" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">Add/ View Departments</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="../view_projects" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">View Projects</span>
              </a>
            </li>
           
            <li class="sidebar-item">
              <a class="sidebar-link" href="../change_password" aria-expanded="false">
                <span>
                  <i class="ti ti-lock"></i>
                </span>
                <span class="hide-menu">Change Password</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="../logout" aria-expanded="false">
                <span>
                  <i class="ti ti-typography"></i>
                </span>
                <span class="hide-menu">Logout</span>
              </a>
            </li>
            
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
        </nav>
      </header>
      <!--  Header End -->
<div class="container content-container">
  <div class="page-header">
    <h2>Admin Dashboard</h2>
  </div>
  <div class="row g-4">

    <div class="col-12 col-sm-6 col-md-4">
      <div class="card stat-card navy">
        <div class="card-body">
          <div class="stat-label">Total Projects</div>
          <div class="stat-value"><?= $totalProjects ?></div>
          <span class="stat-bar"></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
      <div class="card stat-card accent">
        <div class="card-body">
          <div class="stat-label">Departments</div>
          <div class="stat-value"><?= $totalDepartments ?></div>
          <span class="stat-bar"></span>
        </div>
      </div>
    </div>

  </div>

  <!-- Audit Log Section -->
  <div class="mt-5">
    <h4 style="font-weight:800; color:var(--uni-navy); margin-bottom:16px;">Recent Activity Log</h4>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($auditLogs)): ?>
            <tr><td colspan="5" class="text-center text-muted fst-italic">No activity recorded yet.</td></tr>
          <?php else: ?>
            <?php foreach ($auditLogs as $log): ?>
              <tr>
                <td style="white-space:nowrap; font-size:13px;"><?= htmlspecialchars(date('M j, Y g:ia', strtotime($log['created_at']))) ?></td>
                <td><strong><?= htmlspecialchars($log['username']) ?></strong></td>
                <td><span style="display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; background:var(--uni-navy-soft); color:var(--uni-navy);"><?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?></span></td>
                <td class="audit-details"><?= htmlspecialchars($log['details']) ?></td>
                <td style="font-size:12px; font-family:monospace; color:var(--uni-text-muted);"><?= htmlspecialchars($log['ip_address']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>


  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/js/app.min.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>



</html>