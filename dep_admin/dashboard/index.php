<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../dashboard/");
  die();
}
$username = $_SESSION['username'];
$dep_name=$_SESSION['dep_name'];
include "../datacon.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$dep_id = $_SESSION['dep_id']; // To get only your department's projects

// Total projects
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects WHERE dep_id = ?");
$totalQuery->bind_param("s", $dep_id);
$totalQuery->execute();
$totalResult = $totalQuery->get_result()->fetch_assoc();
$totalProjects = $totalResult['total'];


?>



<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Department Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />

  <style>
  .content-container {
    padding-top: 80px; /* adjust if navbar height changes */
  }
   :root {
    --uni-navy: #002147;
    --uni-white: #ffffff;
    --uni-white-soft: rgba(255, 255, 255, 0.75);
  }

  /* TOTAL PROJECTS CARD (NAVY) */
  .bg-primary {
    background: var(--uni-navy) !important;
    color: var(--uni-white) !important;
  }

  /* Label text */
  .bg-primary .card-title {
    color: var(--uni-white-soft);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 12px;
  }

  /* Main number */
  .bg-primary .card-text {
    color: var(--uni-white);
    font-size: 2.6rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    line-height: 1.1;
  }

  /* Optional emphasis line */
  .bg-primary .card-body::after {
    content: "";
    display: block;
    width: 48px;
    height: 3px;
    background-color: rgba(255, 255, 255, 0.4);
    margin-top: 14px;
    border-radius: 2px;
  }
  /* ==========================
   RESPONSIVE DASHBOARD TWEAKS
   ========================== */

/* Global mobile padding fix */
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

/* Tablet adjustments */
@media (max-width: 768px) {
  .content-container {
    padding-top: 90px;
  }

  .card-body {
    padding: 2rem !important;
  }
}

/* Sidebar behavior safety */
@media (max-width: 991px) {
  .left-sidebar {
    position: fixed;
    z-index: 1050;
  }

  .body-wrapper {
    margin-left: 0 !important;
  }
}

/* Improve card spacing consistency */
.card {
  border-radius: 12px;
}

/* Department card text polish */
.bg-warning .card-text {
  font-weight: 700;
  letter-spacing: 0.02em;
}

</style>

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

             <li class="sidebar-item">
              <a class="sidebar-link" href="../view_projects" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">Add/ View Projects</span>
              </a>
            </li>
          
            <li class="sidebar-item">
              <a class="sidebar-link" href="../view_supervisors" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">Add/ View Supervisors</span>
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
    
        </nav>
      </header>
      <!--  Header End -->
<div class="container content-container">

  <div class="row">
 <div class="col-12 col-sm-6 col-md-4">

      <div class="card text-white bg-warning mb-3">
        <div class="card-body py-5">
          <h5 class="card-title">Department</h5>
         <p class="card-text fs-4"><?= htmlspecialchars($dep_name); ?></p>
        </div>
      </div>
    </div>

    <!-- Total Projects -->
  <div class="col-12 col-sm-6 col-md-4">

      <div class="card text-white bg-primary mb-3">
        <div class="card-body py-5">
          <h5 class="card-title">Total Projects</h5>
        <p class="card-text fs-3"><?php echo $totalProjects; ?></p>
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