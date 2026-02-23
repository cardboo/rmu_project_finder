<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../dashboard/");
    die();
}

require "../../app/core/config.php";

// Get selected department ID
$filter = $_GET['department'] ?? '';

$depQuery = "SELECT dep_id, dep_name FROM departments ORDER BY dep_name ASC";
$depResult = $conn->query($depQuery);
$departments = $depResult->fetch_all(MYSQLI_ASSOC);
// Build main query for projects
// $sql = "
// SELECT 
//     p.id AS project_id,
//     p.title,
//     p.synopsis,
//     p.year,
//     p.file_path,
//     d.dep_name,

//     GROUP_CONCAT(DISTINCT CONCAT(pm.student_name, ' (', pm.index_number, ')') SEPARATOR ', ') AS members,
//     GROUP_CONCAT(DISTINCT CONCAT(s.first_name, ' ', s.last_name) SEPARATOR ', ') AS supervisors,
//     GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS tags

// FROM projects p

// LEFT JOIN departments d 
//     ON p.dep_id = d.dep_id

// LEFT JOIN project_members pm 
//     ON p.id = pm.project_id

// LEFT JOIN project_supervisors ps 
//     ON p.id = ps.project_id
// LEFT JOIN supervisors s 
//     ON ps.supervisor_id = s.id

// LEFT JOIN project_tags pt 
//     ON p.id = pt.project_id
// LEFT JOIN tags t 
//     ON pt.tag_id = t.id
// ";

// if (!empty($filter)) {
//     $sql .= " WHERE p.dep_id = ? ";
// }

// $sql .= "
// GROUP BY p.id
// ORDER BY p.year DESC, p.created_at DESC
// ";

// $stmt = $conn->prepare($sql);

// if (!empty($filter)) {
//     $stmt->bind_param("s", $filter);
// }

// $stmt->execute();
// $projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


// ?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Projects </title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

   <style>
  .content-container {
    padding-top: 80px; /* adjust if navbar height changes */
  }
   :root {
    --uni-navy: #002147;
    --uni-navy-soft: rgba(0, 33, 71, 0.06);
    --uni-navy-border: rgba(0, 33, 71, 0.18);
    --uni-text-muted: #5f6f7a;
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

  /* Header above table */
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  gap: 16px;
  flex-wrap: wrap; /* allows stacking on small screens */
}

/* Title */
.table-title {
  margin: 0;
  font-weight: 700;
  color: #002147; /* navy */
}

/* Ensure proper spacing from fixed header */
.content-container {
  padding-top: 80px;
}

/* Wrap heading and dropdown visually */
.content-container > .d-flex {
  position: relative;
}

/* Department filter dropdown */
.content-container select.form-select {
  max-width: 260px;
  margin-left: auto;           /* pushes it to the right */
  margin-bottom: 16px;         /* space before table */
  border: 2px solid #002147;   /* navy */
  font-weight: 600;
  border-radius: 6px;
}

/* Table spacing fix */
.table {
  margin-top: 8px;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .content-container select.form-select {
    max-width: 100%;
    margin-left: 0;
  }
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
                <span class="hide-menu"> View Projects</span>
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
    <h2>View Projects</h2>
  </div>
   <!-- Filters -->
  <div class="row g-2 mb-3">
    <div class="col-md-3">
      <select id="department" class="form-select">
        <option value="">All Departments</option>
        <?php foreach ($departments as $dep): ?>
          <option value="<?= $dep['dep_id'] ?>"><?= htmlspecialchars($dep['dep_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <select id="year" class="form-select">
        <option value="">All Years</option>
        <?php for ($y = date('Y'); $y >= 2015; $y--): ?>
          <option value="<?= $y ?>"><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="col-md-3">
      <input type="text" id="search" class="form-control" placeholder="Search projects...">
    </div>

    <div class="col-md-2">
      <select id="sort" class="form-select">
        <option value="year_desc">Newest</option>
        <option value="year_asc">Oldest</option>
        <option value="title_asc">Title A–Z</option>
        <option value="title_desc">Title Z–A</option>
      </select>
    </div>
  </div>

   

    <!-- Projects Table -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Project Title</th>
                <th>Description</th>
                <th>Year</th>
                <th>Department</th>
                <th>Participant(s)</th>
                <th>Project Supervisor(s)</th>
                <th>Project Tags</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody>
               <?php if ($projects): ?>
                  <?php foreach ($projects as $i => $p): ?>
                  <tr>
                      <td><?= $i + 1 ?></td>
                      <td><?= htmlspecialchars($p['title']) ?></td>
                      <td><?= htmlspecialchars($p['synopsis']) ?></td>
                      <td><?= $p['year'] ?></td>
                      <td><?= htmlspecialchars($p['dep_name']) ?></td>
                      <td><?= $p['members'] ?: '—' ?></td>
                      <td><?= $p['supervisors'] ?: '—' ?></td>
                      <td><?= $p['tags'] ?: '—' ?></td>
                     <td>
                     <button 
                      class="btn btn-sm btn-primary"
                      data-bs-toggle="modal"
                      data-bs-target="#viewDetailsModal"
                      data-title="<?= htmlspecialchars($p['title']) ?>"
                      data-description="<?= htmlspecialchars($p['synopsis']) ?>"
                      data-year="<?= $p['year'] ?>"
                      data-members="<?= htmlspecialchars($p['members']) ?>"
                      data-supervisors="<?= htmlspecialchars($p['supervisors']) ?>"
                      data-tags="<?= htmlspecialchars($p['tags']) ?>"
                      data-file="<?= htmlspecialchars($p['file_path']) ?>"
                    >
                      View
                    </button>

                    </td>

                  </tr>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="9" class="text-center">No projects found</td>
                  </tr>
              <?php endif; ?>
          </tbody>

    </table>
</div>

</body>
</html>

<!-- View Project Modal-->
<!-- View Project Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="viewDetailsModalLabel">Project Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- ✅ YOUR BODY GOES HERE -->
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="detail_title"></span></p>
        <p><strong>Description:</strong> <span id="detail_description"></span></p>
        <p><strong>Year:</strong> <span id="detail_year"></span></p>
        <p><strong>Participant(s):</strong> <span id="detail_members"></span></p>
        <p><strong>Supervisors:</strong> <span id="detail_supervisors"></span></p>
        <p><strong>Tags:</strong> <span id="detail_tags"></span></p>

        <hr>

        <h6 class="fw-bold">Project Abstract (Preview)</h6>

        <div id="pdfPreview" class="border rounded mt-2" style="height: 450px; display:none;">
          <iframe
            id="pdfFrame"
            src=""
            width="100%"
            height="100%"
            style="border:none;"
          ></iframe>
        </div>

        <p id="noPdfText" class="text-muted fst-italic mt-2">
          No file uploaded.
        </p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>







<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




<script>
const viewModal = document.getElementById('viewDetailsModal');

viewModal.addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;

  const title = button.getAttribute('data-title');
  const description = button.getAttribute('data-description');
  const year = button.getAttribute('data-year');
  const members = button.getAttribute('data-members');
  const supervisors = button.getAttribute('data-supervisors');
  const tags = button.getAttribute('data-tags');
  const file = button.getAttribute('data-file'); // filename only

  document.getElementById('detail_title').textContent = title || '';
  document.getElementById('detail_description').textContent = description || '';
  document.getElementById('detail_year').textContent = year || '';
  document.getElementById('detail_members').textContent = members || '—';
  document.getElementById('detail_supervisors').textContent = supervisors || '—';
  document.getElementById('detail_tags').textContent = tags || '—';

  const pdfFrame = document.getElementById('pdfFrame');
  const pdfPreview = document.getElementById('pdfPreview');
  const noPdfText = document.getElementById('noPdfText');

  if (file && file.trim() !== '') {
    // 🔑 CORRECT PATH FROM dep_admin
    pdfFrame.src = "../../dep_admin/uploads/projects/" + file + "#page=1&zoom=100";
    pdfPreview.style.display = "block";
    noPdfText.style.display = "none";
  } else {
    pdfFrame.src = "";
    pdfPreview.style.display = "none";
    noPdfText.style.display = "block";
  }
});
</script>

<script>
const filters = ['department', 'year', 'search', 'sort'];

filters.forEach(id => {
  document.getElementById(id).addEventListener('input', fetchProjects);
});

function fetchProjects() {
  const params = new URLSearchParams({
    department: department.value,
    year: year.value,
    q: search.value,
    sort: sort.value
  });

  fetch('fetch_projects.php?' + params.toString())
    .then(res => res.text())
    .then(html => {
      document.querySelector('tbody').innerHTML = html;
    });
}

// Load initially
fetchProjects();
</script>




</body>
</html>
