<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location:../login/");
    die();
}

include "../datacon.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$dep_id = $_SESSION['dep_id'];
$dep_name = $_SESSION['dep_name'];

/** ✅ Fetch all projects for this department **/
 $sql = "
SELECT 
    p.id AS project_id,
    p.title AS project_title,
    p.synopsis AS description,
    p.year,
    p.file_path,   -- FIXED
    m.student_name,
    m.index_number,
    t.name AS tag_name,
    ps.supervisor_id,
    s.first_name,
    s.last_name

FROM projects p
LEFT JOIN project_members m ON p.id = m.project_id
 LEFT JOIN project_tags pt ON p.id = pt.project_id
 LEFT JOIN tags t ON pt.tag_id = t.id
 LEFT JOIN project_supervisors ps ON p.id = ps.project_id
 LEFT JOIN supervisors s ON ps.supervisor_id = s.id
 WHERE p.dep_id = ?
 ORDER BY p.year DESC, p.id";



$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dep_id);
$stmt->execute();
$projectResult = $stmt->get_result();  // ✅ Separate variable for projects

/** ✅ Fetch supervisors **/
$supervisors = [];
$supervisorResult = $conn->query("SELECT id, first_name, last_name FROM supervisors ORDER BY first_name ASC, last_name ASC");
if ($supervisorResult) {
    while ($row = $supervisorResult->fetch_assoc()) {
        $row['full_name'] = $row['first_name'] . ' ' . $row['last_name']; // concatenate
        $supervisors[] = $row;
    }
}

/** ✅ Build projects array **/
$projects = [];

while ($row = $projectResult->fetch_assoc()) {
    $id = $row['project_id'];

    if (!isset($projects[$id])) {
        $projects[$id] = [
            'id' => $id,
            'project_title' => $row['project_title'],
            'description' => $row['description'],
            'year' => $row['year'],
            'file_path' => $row['file_path'] ?? '', // add file path
            'members' => [],
            'tags' => [],
            'supervisors' => [] // initialize supervisors
        ];
    }

    // Add member
    $member = [
        'student_name' => $row['student_name'],
        'index_number' => $row['index_number']
    ];
    if (!empty($row['student_name']) && !in_array($member, $projects[$id]['members'])) {
        $projects[$id]['members'][] = $member;
    }

    // Add tag
    if (!empty($row['tag_name']) && !in_array($row['tag_name'], $projects[$id]['tags'])) {
        $projects[$id]['tags'][] = $row['tag_name'];
    }

    // Add supervisor (if your query joins supervisor table)
    if (!empty($row['supervisor_id'])) {
        $supervisor = [
            'id' => $row['supervisor_id'],
            'full_name' => $row['first_name'] . ' ' . $row['last_name']
        ];
        if (!in_array($supervisor, $projects[$id]['supervisors'])) {
            $projects[$id]['supervisors'][] = $supervisor;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Projects - <?= htmlspecialchars($dep_name) ?></title>
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
    <h2>Projects for Department: <?= htmlspecialchars($dep_name) ?></h2>
    <div>
      <!-- Buttons trigger modals -->
      <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add a New Project</button>
     
    </div>
  </div>

<!-- Projects Table -->
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Project Title</th>
                <th>Description</th>
                <th>Year</th>
                <th>Participant(s)</th><!-- Added Members column -->
                <th>Supervisor(s)</th>
                <th>Tags</th>    <!-- Added Tags column -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projects)): ?>
                <tr>
                    <td colspan="8" class="text-center">No projects found.</td>
                </tr>
            <?php else: ?>
                <?php $counter = 1; ?>
                <?php foreach ($projects as $proj): ?>
                    <?php
                        // Prepare members list for display
                        $memberList = array_map(function ($m) {
                            return htmlspecialchars($m['student_name'] . ' (' . $m['index_number'] . ')');
                        }, $proj['members']);
                        $membersDisplay = implode(', ', $memberList);

                        // Prepare tags list for display
                        $tagsDisplay = implode(', ', array_map('htmlspecialchars', $proj['tags']));

                       $supervisorDisplay = implode(', ', array_map(fn($s) => htmlspecialchars($s['full_name']), $proj['supervisors']));

                    ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($proj['project_title']) ?></td>
                        <td><?= htmlspecialchars($proj['description']) ?></td>
                        <td><?= htmlspecialchars($proj['year']) ?></td>
                        <td><?= $membersDisplay ?></td>
                       <td><?= $supervisorDisplay ?: 'No supervisor assigned' ?></td>
                        <td><?= $tagsDisplay ?></td>
                        <td>
              <button class="btn btn-sm btn-warning edit-btn"
    data-id="<?= $proj['id'] ?>"
    data-title="<?= htmlspecialchars($proj['project_title'], ENT_QUOTES) ?>"
    data-description="<?= htmlspecialchars($proj['description'], ENT_QUOTES) ?>"
    data-year="<?= $proj['year'] ?>"
    data-tags="<?= htmlspecialchars(implode(',', $proj['tags']), ENT_QUOTES) ?>"
    data-file="<?= htmlspecialchars($proj['file_path'], ENT_QUOTES) ?>"
    data-members='<?= json_encode($proj["members"], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
    data-supervisors='<?= json_encode(array_column($proj["supervisors"], "id"), JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
    data-supervisors-list='<?= json_encode($supervisors, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'
    data-bs-toggle="modal"
    data-bs-target="#editProjectModal">
    Edit
</button>
           
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add Single Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <form action="add_project.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addProjectLabel">Add Past Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="dep_id" value="<?= htmlspecialchars($dep_id) ?>" />
          <div class="mb-3">
            <label for="project_title" class="form-label">Project Title</label>
            <textarea name="project_title" id="project_title" class="form-control" rows="3" required></textarea>
          </div>

          <div class="mb-3">
  <label for="tags" class="form-label">Tags (comma-separated)</label>
  <input type="text" name="tags" id="tags" class="form-control" placeholder="e.g. AI, climate, renewable" />
</div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="number" name="year" id="year" class="form-control" min="1900" max="<?= date("Y") ?>" value="<?= date("Y") ?>" required />
          </div>

                    <div class="mb-3">
            <label class="form-label">Participant(s)</label>
            <div id="members-container">
                <div class="member-row row mb-2">
                <div class="col">
                    <input type="text" name="student_names[]" class="form-control" placeholder="Student Name" required>
                </div>
                <div class="col">
                    <input type="text" name="index_numbers[]" class="form-control" placeholder="Index Number" required>
                </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMemberRow()">+ Add Member</button>
            </div>

            <div class="mb-3">
              <label for="project_file" class="form-label">Upload Project Abstract (PDF ONLY)</label>
              <input type="file" name="project_file" id="project_file" class="form-control" accept=".pdf" />
            </div>

            <div class="mt-3">
  <label class="form-label fw-bold">File Preview</label>

  <div id="filePreviewWrapper" class="border rounded" style="height: 400px; display: none;">
    <iframe
      id="filePreviewFrame"
      width="100%"
      height="100%"
      style="border: none;"
    ></iframe>
  </div>

  <p id="noFilePreview" class="text-muted fst-italic mt-2">
    No file selected.
  </p>
</div>


            <div class="mb-3">
  <label class="form-label">Supervisors</label>
  <div id="supervisors-container">
    <div class="supervisor-row row mb-2">
      <div class="col">
        <select name="supervisors[]" class="form-select" required>
    <option value="">Select Supervisor</option>
    <?php foreach ($supervisors as $sup): ?>
        <option value="<?= $sup['id'] ?>"><?= htmlspecialchars($sup['full_name']) ?></option>
    <?php endforeach; ?>
</select>
      </div>
    </div>
  </div>
  <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addSupervisorRow()">+ Add Supervisor</button>
</div>


      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Project</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>
  <!-- Edit modal -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <form id="editProjectForm" action="update_project.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editProjectLabel">Edit Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Hidden ID -->
        <input type="hidden" name="project_id" id="edit_project_id" />
         <input type="hidden" name="existing_file_path" id="existing_file_path">
        <div class="mb-3">
          <label for="edit_project_title" class="form-label">Project Title</label>
          <textarea name="project_title" id="edit_project_title" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
          <label for="edit_tags" class="form-label">Tags (comma-separated)</label>
          <input type="text" name="tags" id="edit_tags" class="form-control" />
        </div>

        <div class="mb-3">
          <label for="edit_description" class="form-label">Description</label>
          <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
          <label for="edit_year" class="form-label">Year</label>
          <input type="number" name="year" id="edit_year" class="form-control" min="1900" max="<?= date("Y") ?>" required />
        </div>

        <!-- Members -->
        <div class="mb-3">
          <label class="form-label">Participant(s+)</label>
          <div id="edit-members-container"></div>
          <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEditMemberRow()">+ Add Member</button>
        </div>

        <!-- File Upload -->
        <div class="mb-3">
          <label class="form-label">Current File: <span id="current_file"></span></label><br>
          <label for="edit_project_file" class="form-label">Replace File (PDF ONLY)</label>
          <input type="file" name="project_file" id="edit_project_file" class="form-control" accept=".pdf" />
        </div>
       
        <!-- File Preview -->
        <div class="mt-3">
        <label class="form-label fw-bold">Current / New File Preview</label>

        <div id="editFilePreviewWrapper" class="border rounded" style="height: 400px; display: none;">
          <iframe
            id="editFilePreviewFrame"
            width="100%"
            height="100%"
            style="border: none;"
          ></iframe>
        </div>

        <p id="editNoFilePreview" class="text-muted fst-italic mt-2">
          No file available.
        </p>
      </div>


        <!-- Supervisors -->
        <div class="mb-3">
          <label class="form-label">Supervisors</label>
          <div id="edit-supervisors-container"></div>
          <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addEditSupervisorRow()">+ Add Supervisor</button>
        </div>
    
       
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Update Project</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>



<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
  function addMemberRow() {
    const container = document.getElementById("members-container");
    const row = document.createElement("div");
    row.classList.add("member-row", "row", "mb-2");

    row.innerHTML = `
      <div class="col">
        <input type="text" name="student_names[]" class="form-control" placeholder="Student Name" required>
      </div>
      <div class="col">
        <input type="text" name="index_numbers[]" class="form-control" placeholder="Index Number" required>
      </div>
      <div class="col-auto">
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">Remove</button>
      </div>
    `;

    container.appendChild(row);
  }
</script>

<script>
function addEditMemberRow(name = '', index = '') {
    const container = document.getElementById('edit-members-container');
    const memberRow = document.createElement('div');
    memberRow.className = 'member-row row mb-2';
    memberRow.innerHTML = `
        <div class="col">
            <input type="text" name="student_names[]" class="form-control" placeholder="Student Name" value="${name}" required>
        </div>
        <div class="col">
            <input type="text" name="index_numbers[]" class="form-control" placeholder="Index Number" value="${index}" required>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.member-row').remove()">×</button>
        </div>
    `;
    container.appendChild(memberRow);
}

function addEditSupervisorRow(supervisorId = '', supervisorsList = []) {
    const container = document.getElementById('edit-supervisors-container');
    const supervisorRow = document.createElement('div');
    supervisorRow.className = 'supervisor-row row mb-2';

    // Build options dynamically from supervisorsList
    let options = '<option value="">Select Supervisor</option>';
    supervisorsList.forEach(s => {
        const selected = (s.id == supervisorId) ? 'selected' : '';
        options += `<option value="${s.id}" ${selected}>${s.full_name}</option>`;
    });

    supervisorRow.innerHTML = `
        <div class="col">
            <select name="supervisors[]" class="form-select" required>
                ${options}
            </select>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.supervisor-row').remove()">×</button>
        </div>
    `;
    container.appendChild(supervisorRow);
}

document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const title = this.getAttribute('data-title');
        const description = this.getAttribute('data-description');
        const year = this.getAttribute('data-year');
        const tags = this.getAttribute('data-tags');
        const filePath = this.getAttribute('data-file'); // file path from DB
        const membersData = JSON.parse(this.getAttribute('data-members') || '[]');
        const supervisorsData = JSON.parse(this.getAttribute('data-supervisors') || '[]');
        const supervisorsList = JSON.parse(this.getAttribute('data-supervisors-list') || '[]'); // All supervisors from DB

        document.getElementById('edit_project_id').value = id;
        document.getElementById('edit_project_title').value = title;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_year').value = year;
        document.getElementById('edit_tags').value = tags;
      //show current pdf
      const currentFile = document.getElementById('current_file');

if (filePath && filePath.trim() !== '') {
    const filename = filePath.split('/').pop();
    const fileURL = "../uploads/projects/" + filename;

    currentFile.innerHTML = `
        <a href="${fileURL}" target="_blank">${filename}</a>
    `;

    // Required for update_project.php
    document.getElementById("existing_file_path").value = filename;

} else {
    currentFile.textContent = 'No file uploaded';
    document.getElementById("existing_file_path").value = "";
}


        // Populate members
        const memberContainer = document.getElementById('edit-members-container');
        memberContainer.innerHTML = '';
        if (membersData.length > 0) {
            membersData.forEach(member => {
                addEditMemberRow(member.student_name, member.index_number);
            });
        } else {
            addEditMemberRow();
        }

        // Populate supervisors dropdown
        const supervisorContainer = document.getElementById('edit-supervisors-container');
        supervisorContainer.innerHTML = '';
        if (supervisorsData.length > 0) {
            supervisorsData.forEach(supervisorId => {
                addEditSupervisorRow(supervisorId, supervisorsList);
            });
        } else {
            addEditSupervisorRow('', supervisorsList);
        }
    });
});
</script>

<script>

document.querySelectorAll('.edit-btn').forEach(button => {
  button.addEventListener('click', function () {

    const filePath = this.getAttribute('data-file');

    const previewWrapper = document.getElementById('editFilePreviewWrapper');
    const previewFrame = document.getElementById('editFilePreviewFrame');
    const noPreviewText = document.getElementById('editNoFilePreview');

    if (filePath && filePath.trim() !== '') {
      // Build correct path (dep_admin is your base folder)
      const fullPath = '../uploads/projects/' + filePath;

      previewFrame.src = fullPath + '#page=1';
      previewWrapper.style.display = 'block';
      noPreviewText.style.display = 'none';
    } else {
      previewFrame.src = '';
      previewWrapper.style.display = 'none';
      noPreviewText.style.display = 'block';
    }
  });
});



// Preview NEW file when selected in edit modal
document.getElementById('edit_project_file').addEventListener('change', function () {
  const file = this.files[0];

  const previewWrapper = document.getElementById('editFilePreviewWrapper');
  const previewFrame   = document.getElementById('editFilePreviewFrame');
  const noPreviewText  = document.getElementById('editNoFilePreview');

  if (!file) return;

  if (file.type === "application/pdf") {
    const fileURL = URL.createObjectURL(file);
    previewFrame.src = fileURL;
    previewWrapper.style.display = "block";
    noPreviewText.style.display = "none";
  } else {
    previewWrapper.style.display = "none";
    previewFrame.src = "";
    noPreviewText.textContent = "Preview only available for PDF files.";
    noPreviewText.style.display = "block";
  }
});
</script>



<script>
const supervisorOptions = `<?php
foreach ($supervisors as $sup) {
    echo '<option value="' . $sup['id'] . '">' . htmlspecialchars($sup['full_name']) . '</option>';
}
?>`;

function addSupervisorRow() {
    const container = document.getElementById('supervisors-container');
    const newRow = document.createElement('div');
    newRow.classList.add('supervisor-row', 'row', 'mb-2');

    newRow.innerHTML = `
        <div class="col">
            <select name="supervisors[]" class="form-select" required>
                <option value="">Select Supervisor</option>
                ${supervisorOptions}
            </select>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.supervisor-row').remove()">-</button>
        </div>
    `;
    container.appendChild(newRow);
}
</script>

<script>
document.getElementById('project_file').addEventListener('change', function () {
  const file = this.files[0];
  const previewWrapper = document.getElementById('filePreviewWrapper');
  const previewFrame = document.getElementById('filePreviewFrame');
  const noPreviewText = document.getElementById('noFilePreview');

  if (!file) {
    previewWrapper.style.display = 'none';
    noPreviewText.style.display = 'block';
    previewFrame.src = '';
    return;
  }

  // Only allow PDF preview
  if (file.type !== 'application/pdf') {
    alert('Only PDF files can be previewed.');
    this.value = '';
    previewWrapper.style.display = 'none';
    noPreviewText.style.display = 'block';
    previewFrame.src = '';
    return;
  }

  const fileURL = URL.createObjectURL(file);

  previewFrame.src = fileURL + "#page=1";
  previewWrapper.style.display = 'block';
  noPreviewText.style.display = 'none';
});
</script>

<script>
const addProjectModal = document.getElementById('addProjectModal');

addProjectModal.addEventListener('hidden.bs.modal', () => {
  document.getElementById('project_file').value = '';
  document.getElementById('filePreviewFrame').src = '';
  document.getElementById('filePreviewWrapper').style.display = 'none';
  document.getElementById('noFilePreview').style.display = 'block';
});
</script>

</body>
</html>
