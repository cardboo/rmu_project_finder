<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../dashboard/");
    exit();
}

require "../datacon.php";
$dep_id = $_SESSION['dep_id']; 
$dep_name = $_SESSION['dep_name'];

// Fetch departments for dropdown
$depQuery = "SELECT id, dep_name FROM departments ORDER BY dep_name ASC";
$depResult = $conn->query($depQuery);
$departments = $depResult ? $depResult->fetch_all(MYSQLI_ASSOC) : [];



// Fetch supervisors for display
$sql = "SELECT s.id, s.first_name, s.last_name, s.email, d.dep_name
        FROM supervisors s
        LEFT JOIN departments d ON s.dep_id = d.id
        ORDER BY s.id DESC";
$result = $conn->query($sql);
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
  .content-container {
    padding-top: 80px; /* adjust if navbar height changes */
  }
   :root {
    --uni-navy: #002147;
    --uni-navy-soft: rgba(0, 33, 71, 0.06);
    --uni-navy-border: rgba(0, 33, 71, 0.2);
    --uni-text-muted: #6b7c87;
  }

  /* MODAL DIALOG */
  .modal-dialog {
    margin-top: 5vh;
  }

  .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 18px 40px rgba(0, 33, 71, 0.18);
    overflow: hidden;
  }

  /* MODAL HEADER */
  .modal-header {
    background-color: var(--uni-navy);
    color: #ffffff;
    padding: 18px 24px;
    border-bottom: none;
  }

  .modal-title {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }

  .modal-header .btn-close {
    filter: invert(1);
    opacity: 0.9;
  }

  /* MODAL BODY */
  .modal-body {
    padding: 26px 28px;
    background-color: #ffffff;
  }

  /* FORM LABELS */
  .modal-body .form-label {
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--uni-navy);
    margin-bottom: 6px;
  }

  /* FORM INPUTS */
  .modal-body .form-control,
  .modal-body .form-select {
    font-size: 14px;
    border-radius: 8px;
    border: 1.5px solid var(--uni-navy-border);
    padding: 10px 12px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }

  .modal-body .form-control:focus,
  .modal-body .form-select:focus {
    border-color: var(--uni-navy);
    box-shadow: 0 0 0 0.15rem rgba(0, 33, 71, 0.2);
  }

  /* TEXTAREA */
  .modal-body textarea.form-control {
    resize: vertical;
  }

  /* MEMBER & SUPERVISOR ROWS */
  .member-row,
  .supervisor-row {
    background-color: var(--uni-navy-soft);
    padding: 10px;
    border-radius: 8px;
  }

  /* ADD ROW BUTTONS */
  .modal-body .btn-outline-primary {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border-radius: 6px;
    padding: 6px 14px;
    color: var(--uni-navy);
    border-color: var(--uni-navy);
    transition: all 0.2s ease;
  }

  .modal-body .btn-outline-primary:hover {
    background-color: var(--uni-navy);
    color: #ffffff;
  }

  /* FILE INPUT */
  input[type="file"] {
    font-size: 13px;
  }

  /* MODAL FOOTER */
  .modal-footer {
    background-color: #f8fafc;
    padding: 16px 24px;
    border-top: 1px solid var(--uni-navy-soft);
  }

  /* FOOTER BUTTONS */
  .modal-footer .btn {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 8px 18px;
    border-radius: 6px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .modal-footer .btn-primary {
    background-color: var(--uni-navy);
    border-color: var(--uni-navy);
  }

  .modal-footer .btn-primary:hover {
    background-color: #003366;
  }

  .modal-footer .btn-secondary {
    background-color: #e9ecef;
    color: #333;
    border: none;
  }

  .modal-footer .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  }

  /* TABLE WRAPPER */
  .table-responsive {
    margin-top: 30px;
    border-radius: 10px;
    overflow-x: auto;
  }

  /* BASE TABLE */
  table.table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background-color: #ffffff;
    box-shadow: 0 10px 28px rgba(0, 33, 71, 0.08);
    border-radius: 10px;
  }

  /* TABLE HEADER */
  table.table thead.table-dark th {
    background-color: var(--uni-navy) !important;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 14px 12px;
    border: none;
    vertical-align: middle;
  }

  table.table thead.table-dark th:first-child {
    border-top-left-radius: 10px;
  }

  table.table thead.table-dark th:last-child {
    border-top-right-radius: 10px;
  }

  /* TABLE BODY ROWS */
  table.table tbody tr {
    transition: background-color 0.2s ease;
  }

  table.table tbody tr:hover {
    background-color: var(--uni-navy-soft);
  }

  table.table tbody td {
    font-size: 14px;
    color: #2f3f4a;
    padding: 14px 12px;
    vertical-align: middle;
    border-top: 1px solid var(--uni-navy-border);
    line-height: 1.5;
  }

  /* FIRST COLUMN (#) */
  table.table tbody td:first-child {
    font-weight: 600;
    color: var(--uni-navy);
    text-align: center;
    white-space: nowrap;
  }

  /* TITLE COLUMN */
  table.table tbody td:nth-child(2) {
    font-weight: 700;
    color: var(--uni-navy);
    min-width: 220px;
  }

  /* DESCRIPTION COLUMN */
  table.table tbody td:nth-child(3) {
    color: var(--uni-text-muted);
    max-width: 360px;
  }

  /* MEMBERS / SUPERVISORS / TAGS */
  table.table tbody td:nth-child(5),
  table.table tbody td:nth-child(6),
  table.table tbody td:nth-child(7) {
    font-size: 13px;
    color: #425a66;
  }

  /* YEAR COLUMN */
  table.table tbody td:nth-child(4) {
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
  }

  /* ACTIONS COLUMN */
  table.table tbody td:last-child {
    text-align: center;
    white-space: nowrap;
  }

  /* EDIT BUTTON (Bootstrap-friendly) */
  .edit-btn {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 6px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .edit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  }

  /* EMPTY STATE */
  table.table tbody tr td.text-center {
    font-style: italic;
    color: var(--uni-text-muted);
    padding: 24px;
    background-color: #fafafa;
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
      <!--  Header Start -->
      <header class="app-header">
      
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
        <thead class="table-dark">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
