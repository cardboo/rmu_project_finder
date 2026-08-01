<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location:../login/");
  die();
}

include "../datacon.php";
include "../csrf.php";

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



// Fetch all projects for this department
$sql = "SELECT * FROM departments";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add/View Departments  </title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/custom-theme.css" />

  <style>
  table.table tbody td:nth-child(2) { font-weight: 600; color: var(--uni-navy); }
  table.table tbody td:nth-child(3) { font-weight: 600; color: var(--uni-navy); }
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
    <h2>View Active Departments </h2>
    <div>
      <!-- Buttons trigger modals -->
      <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add a Department</button>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">Upload Excel</button>
    </div>
  </div>

 <!-- Departments Table -->
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="">
            <tr>
                <th>#</th>
                <th>Department ID</th>
                <th>Name of Department</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Fetch departments from DB
        $sql = "SELECT id, dep_id, dep_name, username, email FROM departments WHERE is_archived= 0 ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            $index = 1;
            while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $index++ ?></td>
                <td><?= htmlspecialchars($row['dep_id']) ?></td>
                <td><?= htmlspecialchars($row['dep_name']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <button class="btn btn-sm btn-primary edit-btn"
                        data-id="<?= $row['id'] ?>"
                        data-dep_id="<?= htmlspecialchars($row['dep_id'], ENT_QUOTES) ?>"
                        data-dep_name="<?= htmlspecialchars($row['dep_name'], ENT_QUOTES) ?>"
                        data-username="<?= htmlspecialchars($row['username'], ENT_QUOTES) ?>"
                        data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                        data-bs-toggle="modal" data-bs-target="#editDepartmentModal">
                        Edit
                    </button>

                    <form action="archive_department.php" method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to archive this department?');">
                            Archive
                        </button>
                    </form>
                </td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="6" class="text-center">No departments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Add Single Department Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="add_department.php" method="POST" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="addProjectLabel">Add a Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="dep_id" class="form-label">Department ID</label>
          <input type="text" name="dep_id" id="dep_id" class="form-control" placeholder="Enter Department ID" required />
        </div>

        <div class="mb-3">
          <label for="dep_name" class="form-label">Department Name</label>
          <input type="text" name="dep_name" id="dep_name" class="form-control" placeholder="Enter Department Name" required />
        </div>

        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" placeholder="Enter Username" required />
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" required />
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Department</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>


<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editDepartmentForm" method="POST" action="update_department.php" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="id" id="edit_id">

        <div class="mb-3">
          <label for="edit_dep_id" class="form-label">Department ID</label>
          <input type="text" name="dep_id" id="edit_dep_id" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="edit_dep_name" class="form-label">Department Name</label>
          <input type="text" name="dep_name" id="edit_dep_name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="edit_username" class="form-label">Username</label>
          <input type="text" name="username" id="edit_username" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="edit_email" class="form-label">Email</label>
          <input type="email" name="email" id="edit_email" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update</button>
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
document.addEventListener('DOMContentLoaded', function() {
  const editButtons = document.querySelectorAll('.edit-btn');
  
  editButtons.forEach(button => {
    button.addEventListener('click', function() {
      document.getElementById('edit_id').value = this.dataset.id;
      document.getElementById('edit_dep_id').value = this.dataset.dep_id;
      document.getElementById('edit_dep_name').value = this.dataset.dep_name;
      document.getElementById('edit_username').value = this.dataset.username;
      document.getElementById('edit_email').value = this.dataset.email;
    });
  });
});
</script>






</body>
</html>
