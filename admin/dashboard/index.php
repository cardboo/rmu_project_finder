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


// Total projects
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects");
$totalQuery->execute();
$totalProjects = $totalQuery->get_result()->fetch_assoc()['total'];

// Total departments
$deptQuery = $conn->prepare("SELECT COUNT(*) AS total FROM departments");
$deptQuery->execute();
$totalDepartments = $deptQuery->get_result()->fetch_assoc()['total'];
?>


<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />

  <style>
  .content-container {
    padding-top: 80px; /* adjust if navbar height changes */
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

    <!-- Total Projects -->
    <div class="col-md-4">
      <div class="card text-white bg-primary mb-3">
        <div class="card-body py-5">
          <h5 class="card-title">Total Projects</h5>
          <p class="card-text fs-3"><?= $totalProjects ?></p>
        </div>
      </div>
    </div>

    <!-- Total Departments -->
    <div class="col-md-4">
      <div class="card text-white bg-secondary mb-3">
        <div class="card-body py-5">
          <h5 class="card-title">Number of Departments</h5>
          <p class="card-text fs-3"><?= $totalDepartments ?></p>
        </div>
      </div>
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