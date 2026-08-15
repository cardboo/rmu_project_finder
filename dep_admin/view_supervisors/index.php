<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    exit();
}

require "../datacon.php";
require "../csrf.php";
$dep_id = $_SESSION['dep_id'];
$dep_name = $_SESSION['dep_name'];

// Fetch departments for dropdown
$depQuery = "SELECT id, dep_name FROM departments ORDER BY dep_name ASC";
$depResult = $conn->query($depQuery);
$departments = $depResult ? $depResult->fetch_all(MYSQLI_ASSOC) : [];



// Fetch supervisors for display
$sql = "SELECT s.id, s.first_name, s.last_name, s.email, d.dep_name
        FROM supervisors s
        LEFT JOIN departments d ON s.dep_id = d.dep_id
        WHERE s.dep_id = ?
        ORDER BY s.id DESC";
$supStmt = $conn->prepare($sql);
$supStmt->bind_param("s", $dep_id);
$supStmt->execute();
$result = $supStmt->get_result();
$supervisors = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View  Supervisors - <?= htmlspecialchars($dep_name) ?></title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/custom-theme.css" />

  <style>
  table.table tbody td:nth-child(2) { font-weight: 600; color: var(--uni-navy); }
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
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                <i class="ti ti-menu-2"></i>
              </a>
            </li>
          </ul>
        </nav>
      </header>
<div class="container content-container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Supervisors for: <?= htmlspecialchars($dep_name) ?></h2>
    <div>
      <!-- Buttons trigger modals -->
      <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addSupervisorModal">Add a Supervisor</button>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">Upload Excel</button>
    </div>
</div>

<!-- Supervisors Table -->
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="">
            <tr>
                <th>#</th>
                <th>Supervisor Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($supervisors)): ?>
            <tr><td colspan="5" class="text-center">No Supervisors found.</td></tr>
        <?php else: ?>
            <?php foreach ($supervisors as $index => $sup): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?></td>
                <td><?= htmlspecialchars($sup['email']) ?></td>
                <td>
                  <button class="btn btn-primary btn-sm edit-btn" 
        data-id="<?= $sup['id'] ?>" 
        data-first="<?= $sup['first_name'] ?>" 
        data-last="<?= $sup['last_name'] ?>" 
        data-email="<?= $sup['email'] ?>" 
        data-bs-toggle="modal" data-bs-target="#editSupervisorModal">
  Edit
</button>
                    <button class="btn btn-sm btn-danger archive-btn"
                            data-id="<?= $sup['id'] ?>">
                        Archive
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Supervisor Modal -->
<div class="modal fade" id="addSupervisorModal" tabindex="-1" aria-labelledby="addSupervisorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="add_supervisor.php" method="POST" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="addSupervisorLabel">Add A Supervisor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="dep_id" value="<?= htmlspecialchars($dep_id) ?>" />
            
          <div class="mb-3">
            <label class="form-label">Department</label>
            <input type="text" class="form-control" value="<?= $dep_name ?>" readonly>
          </div>

          <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" class="form-control" required>
          </div>
          
          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>

        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Supervisor</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>



<!-- Edit Project Modal -->
<!-- Edit Supervisor Modal -->
<div class="modal fade" id="editSupervisorModal" tabindex="-1" aria-labelledby="editSupervisorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editSupervisorForm" method="POST" action="update_supervisor.php">
      <?= csrf_field() ?>
      <input type="hidden" name="supervisor_id" id="edit_supervisor_id" />
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editSupervisorModalLabel">Edit Supervisor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="edit_first_name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="edit_last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="edit_last_name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label for="edit_email" class="form-label">Email</label>
            <input type="email" name="email" id="edit_email" class="form-control" required />
          </div>
         
       
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save Changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- Upload Excel Modal -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-labelledby="uploadExcelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="upload_excel.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="uploadExcelLabel">Upload Projects Excel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="dep_id" value="<?= htmlspecialchars($dep_id) ?>" />
        <div class="mb-3">
          <label for="excel_file" class="form-label">Select Excel File</label>
          <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xls,.xlsx" required />
          <small class="form-text text-muted">Upload .xls or .xlsx file with projects data.</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Upload</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
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
  new MutationObserver(function() { o.classList.toggle('active', w.classList.contains('show-sidebar')); }).observe(w, { attributes: true, attributeFilter: ['class'] });
  o.addEventListener('click', function() { w.classList.remove('show-sidebar'); o.classList.remove('active'); });
})();
</script>

<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_supervisor_id').value = this.dataset.id;
        document.getElementById('edit_first_name').value = this.dataset.first;
        document.getElementById('edit_last_name').value = this.dataset.last;
        document.getElementById('edit_email').value = this.dataset.email;
    });
});
</script>

</body>
</html>
