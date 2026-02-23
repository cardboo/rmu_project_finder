<?php
/**
 * Department Admin Dashboard View
 * 
 * Displays the department admin dashboard with key metrics.
 * Variables available: $username, $totalProjects, $dep_name
 * 
 * Path: modules/dep_admin/dashboard/dashboard.view.php
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Department Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../../dep_admin/assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../../dep_admin/assets/css/styles.min.css" />
  <style>
    :root {
      --uni-navy: #002147;
      --uni-white: #ffffff;
      --uni-white-soft: rgba(255, 255, 255, 0.75);
    }

    .bg-primary {
      background: var(--uni-navy) !important;
      color: var(--uni-white) !important;
    }

    .bg-primary .card-title {
      color: var(--uni-white-soft);
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      margin-bottom: 12px;
    }

    .bg-primary .card-text {
      color: var(--uni-white);
      font-size: 2.6rem;
      font-weight: 900;
      letter-spacing: -0.02em;
      line-height: 1.1;
    }

    .content-container {
      padding-top: 80px;
    }

    @media (max-width: 576px) {
      .content-container {
        padding-top: 100px;
        padding-left: 12px;
        padding-right: 12px;
      }

      .card-body {
        padding: 1.5rem !important;
      }

      .card-title {
        font-size: 11px;
      }

      .card-text {
        font-size: 1.8rem !important;
      }
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
            <img src="../../dep_admin/assets/images/logos/RMU_logo.png" width="180" height="60" alt="RMU Logo" />
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
              <a class="sidebar-link" href="../../dep_admin/dashboard" aria-expanded="false">
                <span><i class="ti ti-layout-dashboard"></i></span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../dep_admin/view_projects" aria-expanded="false">
                <span><i class="ti ti-article"></i></span>
                <span class="hide-menu">Add/View Projects</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../dep_admin/view_supervisors" aria-expanded="false">
                <span><i class="ti ti-article"></i></span>
                <span class="hide-menu">Add/View Supervisors</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../../dep_admin/logout" aria-expanded="false">
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
          <!-- Department Name -->
          <div class="col-12 col-sm-6 col-md-4">
            <div class="card text-white bg-warning mb-3">
              <div class="card-body py-5">
                <h5 class="card-title">Department</h5>
                <p class="card-text fs-4"><?php echo htmlspecialchars($dep_name); ?></p>
              </div>
            </div>
          </div>

          <!-- Total Projects -->
          <div class="col-12 col-sm-6 col-md-4">
            <div class="card text-white bg-primary mb-3">
              <div class="card-body py-5">
                <h5 class="card-title">Total Projects</h5>
                <p class="card-text fs-3"><?php echo htmlspecialchars($totalProjects); ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../../dep_admin/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../../dep_admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../dep_admin/assets/js/sidebarmenu.js"></script>
  <script src="../../dep_admin/assets/js/app.min.js"></script>
  <script src="../../dep_admin/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../../dep_admin/assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../../dep_admin/assets/js/dashboard.js"></script>
</body>
</html>
