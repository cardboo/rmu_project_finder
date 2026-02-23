<?php
/**
 * Admin Dashboard View
 * 
 * Displays the admin dashboard with key metrics and layout.
 * Variables available: $username, $totalProjects, $totalDepartments
 * 
 * Path: modules/admin/dashboard/dashboard.view.php
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../../admin/assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../../admin/assets/css/styles.min.css" />
  <style>
    .content-container {
      padding-top: 80px;
    }
  </style>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.html" class="text-nowrap logo-img">
            <img src="../../admin/assets/images/logos/RMU_logo.png" width="180" height="60" alt="RMU Logo" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../admin/dashboard" aria-expanded="false">
                <span><i class="ti ti-layout-dashboard"></i></span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../admin/view_departments" aria-expanded="false">
                <span><i class="ti ti-article"></i></span>
                <span class="hide-menu">Add/View Departments</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../admin/view_projects" aria-expanded="false">
                <span><i class="ti ti-article"></i></span>
                <span class="hide-menu">View Projects</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../admin/logout" aria-expanded="false">
                <span><i class="ti ti-typography"></i></span>
                <span class="hide-menu">Logout</span>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    </aside>
    
    <!-- Main wrapper -->
    <div class="body-wrapper">
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light"></nav>
      </header>

      <div class="container content-container">
        <div class="row">
          <!-- Total Projects -->
          <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-body py-5">
                <h5 class="card-title">Total Projects</h5>
                <p class="card-text fs-3"><?php echo htmlspecialchars($totalProjects); ?></p>
              </div>
            </div>
          </div>

          <!-- Total Departments -->
          <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
              <div class="card-body py-5">
                <h5 class="card-title">Number of Departments</h5>
                <p class="card-text fs-3"><?php echo htmlspecialchars($totalDepartments); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../../admin/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../../admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../admin/assets/js/sidebarmenu.js"></script>
  <script src="../../admin/assets/js/app.min.js"></script>
  <script src="../../admin/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../../admin/assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../../admin/assets/js/dashboard.js"></script>
</body>
</html>
