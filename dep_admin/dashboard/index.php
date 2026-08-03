<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}
$username = $_SESSION['username'];
$dep_name=$_SESSION['dep_name'];
include "../datacon.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$dep_id = $_SESSION['dep_id'];

// Total projects (exclude archived)
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects WHERE dep_id = ? AND (is_archived = 0 OR is_archived IS NULL)");
$totalQuery->bind_param("s", $dep_id);
$totalQuery->execute();
$totalResult = $totalQuery->get_result()->fetch_assoc();
$totalProjects = $totalResult['total'];

// Projects by year for this department (for chart)
$yearChartQuery = $conn->prepare("
  SELECT year, COUNT(*) AS project_count
  FROM projects
  WHERE dep_id = ? AND (is_archived = 0 OR is_archived IS NULL)
  GROUP BY year
  ORDER BY year ASC
");
$yearChartQuery->bind_param("s", $dep_id);
$yearChartQuery->execute();
$yearChartData = $yearChartQuery->get_result()->fetch_all(MYSQLI_ASSOC);

// Recent activity for this department admin
$auditQuery = $conn->prepare("SELECT username, action, details, ip_address, created_at FROM audit_log WHERE username = ? ORDER BY created_at DESC LIMIT 15");
$auditQuery->bind_param("s", $username);
$auditQuery->execute();
$auditLogs = $auditQuery->get_result()->fetch_all(MYSQLI_ASSOC);
?>



<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Department Dashboard</title>
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
          <div class="close-btn d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
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
              <a class="sidebar-link" href="../change_password" aria-expanded="false">
                <span>
                  <i class="ti ti-lock"></i>
                </span>
                <span class="hide-menu">Change Password</span>
              </a>
            </li>

            <li class="sidebar-item">
              <a class="sidebar-link" href="../logout" aria-expanded="false" onclick="return confirm('Are you sure you want to logout?')">
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
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block">
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
    <h2>Department Dashboard</h2>
  </div>
  <div class="row g-4">

    <div class="col-12 col-sm-6 col-md-4">
      <div class="card stat-card light">
        <div class="card-body">
          <div class="stat-label">Department</div>
          <div class="stat-value"><?= htmlspecialchars($dep_name); ?></div>
          <span class="stat-bar"></span>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-4">
      <div class="card stat-card navy">
        <div class="card-body">
          <div class="stat-label">Total Projects</div>
          <div class="stat-value"><?= $totalProjects ?></div>
          <span class="stat-bar"></span>
        </div>
      </div>
    </div>

  </div>

  <!-- Chart Section -->
  <div class="row g-4 mt-2">
    <div class="col-12 col-lg-8">
      <div class="card">
        <div class="card-body">
          <h5 class="section-heading">Projects by Year</h5>
          <div id="yearChart"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Activity Log Section -->
  <div class="mt-5">
    <h4 class="section-heading">Your Recent Activity</h4>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead class="">
          <tr>
            <th>Time</th>
            <th>Action</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($auditLogs)): ?>
            <tr><td colspan="3" class="text-center text-muted fst-italic">No activity recorded yet.</td></tr>
          <?php else: ?>
            <?php foreach ($auditLogs as $log): ?>
              <tr>
                <td class="audit-time"><?= htmlspecialchars(date('M j, Y g:ia', strtotime($log['created_at']))) ?></td>
                <td><span class="audit-badge"><?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?></span></td>
                <td class="audit-details"><?= htmlspecialchars($log['details']) ?></td>
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
  <script>
  (function() {
    var w = document.getElementById('main-wrapper'), o = document.getElementById('sidebarOverlay');
    if (!w || !o) return;
    new MutationObserver(function() { if (window.innerWidth < 1200) o.classList.toggle('active', w.classList.contains('show-sidebar')); }).observe(w, { attributes: true, attributeFilter: ['class'] });
    o.addEventListener('click', function() { w.classList.remove('show-sidebar'); w.classList.remove('mini-sidebar'); w.setAttribute('data-sidebartype','full'); o.classList.remove('active'); });
  })();
  </script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script>
  <?php if (!empty($yearChartData)): ?>
  var yearOptions = {
    chart: { type: 'bar', height: 300, toolbar: { show: false } },
    series: [{ name: 'Projects', data: <?= json_encode(array_map('intval', array_column($yearChartData, 'project_count'))) ?> }],
    xaxis: { categories: <?= json_encode(array_column($yearChartData, 'year')) ?> },
    colors: ['#1e3a5c'],
    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
    dataLabels: { enabled: true, style: { fontSize: '12px', fontWeight: 700 } },
    tooltip: { y: { formatter: function(val) { return val + ' project' + (val !== 1 ? 's' : ''); } } }
  };
  new ApexCharts(document.querySelector("#yearChart"), yearOptions).render();
  <?php else: ?>
  document.querySelector("#yearChart").innerHTML = '<p class="text-muted text-center py-4">No project data to display yet.</p>';
  <?php endif; ?>
  </script>
</body>



</html>