<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}

require "../datacon.php";

// Get selected department ID
$filter = $_GET['department'] ?? '';

$depQuery = "SELECT dep_id, dep_name FROM departments ORDER BY dep_name ASC";
$depResult = $conn->query($depQuery);
$departments = $depResult->fetch_all(MYSQLI_ASSOC);

// Fetch all supervisors for filter dropdown
$supQuery = "SELECT DISTINCT s.id, CONCAT(s.first_name, ' ', s.last_name) AS full_name FROM supervisors s ORDER BY s.first_name ASC";
$supResult = $conn->query($supQuery);
$allSupervisors = $supResult->fetch_all(MYSQLI_ASSOC);

// Fetch all tags for filter dropdown
$tagQuery = "SELECT id, name FROM tags ORDER BY name ASC";
$tagResult = $conn->query($tagQuery);
$allTags = $tagResult->fetch_all(MYSQLI_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Projects </title>
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/custom-theme.css" />

  <style>
  /* Page-specific column styles */
  table.table tbody td:first-child { font-weight: 600; color: var(--uni-navy); text-align: center; white-space: nowrap; }
  table.table tbody td:nth-child(2) { font-weight: 700; color: var(--uni-navy); min-width: 220px; }
  table.table tbody td:nth-child(3) { color: var(--uni-text-muted); max-width: 360px; }
  table.table tbody td:nth-child(4) { font-weight: 600; text-align: center; white-space: nowrap; }
  table.table tbody td:nth-child(5),
  table.table tbody td:nth-child(6),
  table.table tbody td:nth-child(7) { font-size: 13px; color: #425a66; }
  table.table tbody td:last-child { text-align: center; white-space: nowrap; }
  .content-container select.form-select { max-width: 260px; margin-left: auto; margin-bottom: 16px; border: 2px solid var(--uni-navy); font-weight: 600; border-radius: 6px; }
  @media (max-width: 768px) {
    .content-container select.form-select { max-width: 100%; margin-left: 0; }
  }
  .modal-dialog { margin-top: 5vh; }
  textarea.form-control { resize: vertical; }
  input[type="file"] { font-size: 13px; }
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
<div class="container content-container">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>View Projects</h2>
    <button class="btn btn-outline-primary btn-sm" onclick="exportCSV()">Export CSV</button>
  </div>

  <!-- Filters Row 1 -->
  <div class="row g-2 mb-2">
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
      <select id="supervisor" class="form-select">
        <option value="">All Supervisors</option>
        <?php foreach ($allSupervisors as $sup): ?>
          <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['full_name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <select id="tag" class="form-select">
        <option value="">All Tags</option>
        <?php foreach ($allTags as $tag): ?>
          <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2">
      <select id="sort" class="form-select">
        <option value="year_desc">Newest First</option>
        <option value="year_asc">Oldest First</option>
        <option value="title_asc">Title A-Z</option>
        <option value="title_desc">Title Z-A</option>
        <option value="dep_asc">Department A-Z</option>
        <option value="dep_desc">Department Z-A</option>
      </select>
    </div>
  </div>

  <!-- Filters Row 2: Search + Result Count -->
  <div class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="text" id="search" class="form-control" placeholder="Search by title, description, or tag...">
    </div>
    <div class="col-md-8 d-flex align-items-center">
      <span id="resultCount" class="text-muted fw-bold ms-2"></span>
    </div>
  </div>

   

    <!-- Projects Table -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th style="cursor:pointer" onclick="sortByColumn('title_asc','title_desc')">Project Title <span class="sort-icon">&#8693;</span></th>
                <th>Description</th>
                <th style="cursor:pointer" onclick="sortByColumn('year_asc','year_desc')">Year <span class="sort-icon">&#8693;</span></th>
                <th style="cursor:pointer" onclick="sortByColumn('dep_asc','dep_desc')">Department <span class="sort-icon">&#8693;</span></th>
                <th>Participant(s)</th>
                <th>Project Supervisor(s)</th>
                <th>Project Tags</th>
                <th>Actions</th>

            </tr>
        </thead>
        <tbody>
            <!-- Populated via AJAX fetchProjects() -->
        </tbody>

    </table>

    <!-- Pagination -->
    <nav id="paginationNav" class="d-flex justify-content-center mt-3"></nav>
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
let adminCurrentPage = 1;
const filters = ['department', 'year', 'search', 'sort', 'supervisor', 'tag'];

filters.forEach(id => {
  const el = document.getElementById(id);
  if (el) el.addEventListener('input', () => { adminCurrentPage = 1; fetchProjects(); });
});

function fetchProjects(page) {
  if (page !== undefined) adminCurrentPage = page;

  const params = new URLSearchParams({
    department: document.getElementById('department').value,
    year: document.getElementById('year').value,
    q: document.getElementById('search').value,
    sort: document.getElementById('sort').value,
    supervisor: document.getElementById('supervisor').value,
    tag: document.getElementById('tag').value,
    page: adminCurrentPage
  });

  fetch('fetch_projects.php?' + params.toString())
    .then(res => {
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.text();
    })
    .then(html => {
      document.querySelector('tbody').innerHTML = html;

      // Extract pagination data
      const paginationRow = document.querySelector('tbody .pagination-data');
      let total = 0, currentPage = 1, totalPages = 0;
      if (paginationRow) {
        total = parseInt(paginationRow.dataset.total) || 0;
        currentPage = parseInt(paginationRow.dataset.page) || 1;
        totalPages = parseInt(paginationRow.dataset.pages) || 0;
      }

      const countEl = document.getElementById('resultCount');
      countEl.textContent = total > 0 ? total + ' project' + (total !== 1 ? 's' : '') + ' found' : '0 projects found';

      renderAdminPagination(currentPage, totalPages);
    })
    .catch(error => {
      console.error('Error fetching projects:', error);
      document.querySelector('tbody').innerHTML = '<tr><td colspan="9" class="text-center">Error loading projects</td></tr>';
      document.getElementById('resultCount').textContent = '';
      document.getElementById('paginationNav').innerHTML = '';
    });
}

function renderAdminPagination(current, totalPages) {
  const nav = document.getElementById('paginationNav');
  if (totalPages <= 1) { nav.innerHTML = ''; return; }

  let html = '<ul class="pagination mb-0">';
  html += `<li class="page-item ${current === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchProjects(${current - 1});">&laquo;</a></li>`;

  const pages = [];
  pages.push(1);
  if (current > 3) pages.push('...');
  for (let i = Math.max(2, current - 1); i <= Math.min(totalPages - 1, current + 1); i++) {
    pages.push(i);
  }
  if (current < totalPages - 2) pages.push('...');
  if (totalPages > 1) pages.push(totalPages);

  pages.forEach(p => {
    if (p === '...') {
      html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
    } else {
      html += `<li class="page-item ${p === current ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchProjects(${p});">${p}</a></li>`;
    }
  });

  html += `<li class="page-item ${current === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); fetchProjects(${current + 1});">&raquo;</a></li>`;
  html += '</ul>';
  nav.innerHTML = html;
}

// Column header sort toggle
function sortByColumn(ascVal, descVal) {
  const sortEl = document.getElementById('sort');
  sortEl.value = (sortEl.value === ascVal) ? descVal : ascVal;
  adminCurrentPage = 1;
  fetchProjects();
}

// Export visible table data as CSV
function exportCSV() {
  const rows = document.querySelectorAll('tbody tr');
  if (!rows.length) return;

  const headers = ['#', 'Project Title', 'Description', 'Year', 'Department', 'Participants', 'Supervisors', 'Tags'];
  let csv = headers.map(h => '"' + h + '"').join(',') + '\n';

  rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    if (cells.length < 8) return; // skip "no results" row
    const rowData = [];
    for (let i = 0; i < 8; i++) {
      rowData.push('"' + (cells[i].textContent || '').replace(/"/g, '""').trim() + '"');
    }
    csv += rowData.join(',') + '\n';
  });

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'projects_report_' + new Date().toISOString().slice(0,10) + '.csv';
  a.click();
  URL.revokeObjectURL(url);
}

// Load initially
fetchProjects();
</script>




</body>
</html>
