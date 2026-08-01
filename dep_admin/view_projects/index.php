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

$dep_id = $_SESSION['dep_id'];
$dep_name = $_SESSION['dep_name'];

/** Fetch all projects for this department **/
$showArchived = isset($_GET['show_archived']) && $_GET['show_archived'] === '1';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

$sql = "
SELECT
    p.id AS project_id,
    p.title AS project_title,
    p.synopsis AS description,
    p.year,
    p.file_path,
    p.is_archived,
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
ORDER BY p.is_archived ASC, p.year DESC, p.id";



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
            'file_path' => $row['file_path'] ?? '',
            'is_archived' => $row['is_archived'] ?? 0,
            'members' => [],
            'tags' => [],
            'supervisors' => []
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
  <link rel="stylesheet" href="../assets/css/custom-theme.css" />

  <style>
  /* Page-specific column styles */
  table.table tbody td:first-child { font-weight: 600; color: var(--uni-navy); text-align: center; white-space: nowrap; }
  table.table tbody td:nth-child(2) { font-weight: 700; color: var(--uni-navy); }
  table.table tbody td:nth-child(3) { color: var(--uni-text-muted); }
  table.table tbody td:nth-child(4) { font-weight: 600; text-align: center; white-space: nowrap; }
  table.table tbody td:nth-child(5),
  table.table tbody td:nth-child(6),
  table.table tbody td:nth-child(7) { font-size: 13px; color: #425a66; }
  table.table tbody td:last-child { text-align: center; white-space: nowrap; }
  .edit-btn { font-size: 12px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; padding: 6px 14px; border-radius: 6px; transition: transform 0.15s, box-shadow 0.15s; }
  .edit-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); }
  table.table tbody tr td.text-center { font-style: italic; color: var(--uni-text-muted); padding: 24px; background-color: #fafafa; }
  @media (min-width: 769px) {
    .modal-dialog { margin-top: 5vh; }
  }
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

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h2>Projects for Department: <?= htmlspecialchars($dep_name) ?></h2>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="?show_archived=<?= $showArchived ? '0' : '1' ?>" class="btn <?= $showArchived ? 'btn-outline-secondary' : 'btn-outline-primary' ?> btn-sm">
        <?= $showArchived ? 'Hide Archived' : 'Show Archived' ?>
      </a>
      <button class="btn btn-info text-white" onclick="exportCSV()">Export CSV</button>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add a New Project</button>
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
            <?php
                // Separate active and archived
                $activeProjects = array_filter($projects, fn($p) => !$p['is_archived']);
                $archivedProjects = array_filter($projects, fn($p) => $p['is_archived']);
                $allDisplayProjects = $showArchived ? $projects : $activeProjects;
                $totalProj = count($allDisplayProjects);
                $totalPages = ceil($totalProj / $perPage);
                $page = min($page, max(1, $totalPages));
                $displayProjects = array_slice(array_values($allDisplayProjects), ($page - 1) * $perPage, $perPage);
                $startNum = ($page - 1) * $perPage;
            ?>
            <?php if (empty($displayProjects)): ?>
                <tr>
                    <td colspan="8" class="text-center">No projects found.</td>
                </tr>
            <?php else: ?>
                <?php $counter = $startNum + 1; ?>
                <?php foreach ($displayProjects as $proj): ?>
                    <?php
                        $memberList = array_map(function ($m) {
                            return htmlspecialchars($m['student_name'] . ' (' . $m['index_number'] . ')');
                        }, $proj['members']);
                        $membersDisplay = implode(', ', $memberList);
                        $tagsDisplay = implode(', ', array_map('htmlspecialchars', $proj['tags']));
                        $supervisorDisplay = implode(', ', array_map(fn($s) => htmlspecialchars($s['full_name']), $proj['supervisors']));
                        $isArchived = $proj['is_archived'];
                    ?>
                    <tr<?= $isArchived ? ' style="opacity:0.6;"' : '' ?>>
                        <td><?= $counter++ ?></td>
                        <td>
                            <?= htmlspecialchars($proj['project_title']) ?>
                            <?php if ($isArchived): ?>
                                <span style="display:inline-block; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:700; background:#dc3545; color:#fff; margin-left:6px; vertical-align:middle;">ARCHIVED</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($proj['description']) ?></td>
                        <td><?= htmlspecialchars($proj['year']) ?></td>
                        <td><?= $membersDisplay ?></td>
                        <td><?= $supervisorDisplay ?: 'No supervisor assigned' ?></td>
                        <td><?= $tagsDisplay ?></td>
                        <td style="white-space:nowrap;">
                            <?php if (!$isArchived): ?>
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
                                <form action="archive_project.php" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to archive this project?');">Archive</button>
                                </form>
                            <?php else: ?>
                                <form action="unarchive_project.php" method="POST" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= $proj['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Restore this project?');">Unarchive</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <nav class="d-flex justify-content-center mt-3">
      <ul class="pagination mb-0">
        <?php
          // Build current URL params (preserve show_archived)
          $urlParams = [];
          if ($showArchived) $urlParams['show_archived'] = '1';
        ?>
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($urlParams, ['page' => $page - 1])) ?>">&laquo;</a>
        </li>
        <?php
          $pages = [1];
          if ($page > 3) $pages[] = '...';
          for ($i = max(2, $page - 1); $i <= min($totalPages - 1, $page + 1); $i++) {
            $pages[] = $i;
          }
          if ($page < $totalPages - 2) $pages[] = '...';
          if ($totalPages > 1) $pages[] = $totalPages;

          foreach ($pages as $pg):
            if ($pg === '...'):
        ?>
              <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php else: ?>
              <li class="page-item <?= $pg === $page ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($urlParams, ['page' => $pg])) ?>"><?= $pg ?></a>
              </li>
        <?php
            endif;
          endforeach;
        ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?<?= http_build_query(array_merge($urlParams, ['page' => $page + 1])) ?>">&raquo;</a>
        </li>
      </ul>
    </nav>
    <?php endif; ?>

    <p class="text-center text-muted mt-2 mb-0" style="font-size:13px;">
      Showing <?= $startNum + 1 ?>–<?= min($startNum + $perPage, $totalProj) ?> of <?= $totalProj ?> project<?= $totalProj !== 1 ? 's' : '' ?>
    </p>
</div>

<!-- Add Single Project Modal -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <form action="add_project.php" method="POST" enctype="multipart/form-data" class="modal-content">
      <?= csrf_field() ?>
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

  <div id="filePreviewWrapper" class="border rounded pdf-preview-container" style="display: none;">
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
        <select name="supervisors[]" class="form-select" required onchange="refreshSupervisorDropdowns('supervisors-container')">
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
      <?= csrf_field() ?>
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

        <div id="editFilePreviewWrapper" class="border rounded pdf-preview-container" style="display: none;">
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
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

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

// Global variable to store supervisors list for the edit modal
let currentEditSupervisorsList = [];

function addEditSupervisorRow(supervisorId = '', supervisorsList = null) {
    // Use global list if none provided (e.g. from "+ Add Supervisor" button)
    if (!supervisorsList || supervisorsList.length === 0) {
        supervisorsList = currentEditSupervisorsList;
    }
    const container = document.getElementById('edit-supervisors-container');
    const takenIds = getSelectedSupervisorIds('edit-supervisors-container', null);
    const supervisorRow = document.createElement('div');
    supervisorRow.className = 'supervisor-row row mb-2';

    // Build options dynamically, omitting already-selected supervisors
    let options = '<option value="">Select Supervisor</option>';
    supervisorsList.forEach(s => {
        const sid = String(s.id);
        const isCurrentRow = (sid == supervisorId);
        if (!takenIds.includes(sid) || isCurrentRow) {
            const selected = isCurrentRow ? 'selected' : '';
            options += `<option value="${s.id}" ${selected}>${s.full_name}</option>`;
        }
    });

    supervisorRow.innerHTML = `
        <div class="col">
            <select name="supervisors[]" class="form-select" required onchange="refreshSupervisorDropdowns('edit-supervisors-container', currentEditSupervisorsList)">
                ${options}
            </select>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.supervisor-row').remove(); refreshSupervisorDropdowns('edit-supervisors-container', currentEditSupervisorsList)">×</button>
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

        // Store globally so "+ Add Supervisor" button can use it
        currentEditSupervisorsList = supervisorsList;

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
const allSupervisorsAdd = <?= json_encode($supervisors, JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

// Get currently selected supervisor IDs in a container
function getSelectedSupervisorIds(containerId, excludeSelect) {
    const selects = document.querySelectorAll('#' + containerId + ' select[name="supervisors[]"]');
    const ids = [];
    selects.forEach(s => {
        if (s !== excludeSelect && s.value) ids.push(s.value);
    });
    return ids;
}

// Refresh all supervisor dropdowns in a container to hide already-picked options
function refreshSupervisorDropdowns(containerId, supervisorsList) {
    const list = supervisorsList || allSupervisorsAdd;
    const selects = document.querySelectorAll('#' + containerId + ' select[name="supervisors[]"]');
    selects.forEach(sel => {
        const currentVal = sel.value;
        const takenIds = getSelectedSupervisorIds(containerId, sel);
        sel.innerHTML = '<option value="">Select Supervisor</option>';
        list.forEach(s => {
            const sid = String(s.id);
            if (!takenIds.includes(sid) || sid === currentVal) {
                const opt = document.createElement('option');
                opt.value = sid;
                opt.textContent = s.full_name;
                if (sid === currentVal) opt.selected = true;
                sel.appendChild(opt);
            }
        });
    });
}

function addSupervisorRow() {
    const container = document.getElementById('supervisors-container');
    const takenIds = getSelectedSupervisorIds('supervisors-container', null);
    const newRow = document.createElement('div');
    newRow.classList.add('supervisor-row', 'row', 'mb-2');

    let options = '<option value="">Select Supervisor</option>';
    allSupervisorsAdd.forEach(s => {
        if (!takenIds.includes(String(s.id))) {
            options += `<option value="${s.id}">${s.full_name}</option>`;
        }
    });

    newRow.innerHTML = `
        <div class="col">
            <select name="supervisors[]" class="form-select" required onchange="refreshSupervisorDropdowns('supervisors-container')">
                ${options}
            </select>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.supervisor-row').remove(); refreshSupervisorDropdowns('supervisors-container')">-</button>
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

<script>
// Auto-generate tags from project title keywords
(function() {
  const stopWords = new Set([
    'the','a','an','and','or','but','in','on','at','to','for','of','with','by',
    'from','as','is','was','are','were','be','been','being','have','has','had',
    'do','does','did','will','would','shall','should','can','could','may','might',
    'must','that','this','these','those','it','its','not','no','so','if','then',
    'than','too','very','just','about','above','after','again','all','also','am',
    'any','because','before','between','both','during','each','few','further',
    'here','how','into','more','most','other','our','out','own','same','some',
    'such','there','through','under','until','up','what','when','where','which',
    'while','who','whom','why','you','your','using','based','study','analysis',
    'development','design','system','project','research','implementation','use'
  ]);

  function generateTags(title) {
    if (!title.trim()) return '';
    const words = title.split(/[\s\-\/,;:()]+/)
      .map(w => w.replace(/[^a-zA-Z0-9]/g, '').toLowerCase())
      .filter(w => w.length > 2 && !stopWords.has(w));
    const unique = [...new Set(words)];
    return unique.slice(0, 6).join(', ');
  }

  // Add project modal - auto-generate tags on title blur
  const addTitle = document.getElementById('project_title');
  const addTags = document.getElementById('tags');
  if (addTitle && addTags) {
    addTitle.addEventListener('blur', function() {
      if (addTags.value.trim() === '') {
        addTags.value = generateTags(this.value);
      }
    });
  }

  // Edit project modal - auto-generate tags on title blur if tags are empty
  const editTitle = document.getElementById('edit_project_title');
  const editTags = document.getElementById('edit_tags');
  if (editTitle && editTags) {
    editTitle.addEventListener('blur', function() {
      if (editTags.value.trim() === '') {
        editTags.value = generateTags(this.value);
      }
    });
  }
})();
</script>

<script>
function exportCSV() {
  const table = document.querySelector('table.table');
  if (!table) return;
  const rows = table.querySelectorAll('tr');
  let csv = [];
  rows.forEach((row, i) => {
    const cells = row.querySelectorAll('th, td');
    if (cells.length < 2) return;
    const lastIdx = cells.length - 1;
    const rowData = [];
    cells.forEach((cell, j) => {
      if (j === lastIdx && i > 0) return;
      if (j === lastIdx && i === 0) { rowData.push('"Status"'); return; }
      let text = cell.textContent.trim().replace(/\s+/g, ' ');
      rowData.push('"' + text.replace(/"/g, '""') + '"');
    });
    if (i > 0) {
      const archived = row.style.opacity === '0.6' ? 'Archived' : 'Active';
      rowData.push('"' + archived + '"');
    }
    csv.push(rowData.join(','));
  });
  const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = '<?= preg_replace("/[^a-zA-Z0-9]/", "_", $dep_name) ?>_projects.csv';
  link.click();
}
</script>

</body>
</html>
