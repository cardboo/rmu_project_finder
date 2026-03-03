<?php
session_start();
require "../datacon.php";

// Ensure user is logged in as admin
if (!isset($_SESSION['username'])) {
    exit('Unauthorized');
}

// Optional filters from UI
$department = $_GET['department'] ?? '';
$year = $_GET['year'] ?? '';
$q    = $_GET['q'] ?? '';
$sort = $_GET['sort'] ?? 'year_desc';

// ============================
// Build WHERE conditions
// ============================
$where  = ["(p.is_archived = 0 OR p.is_archived IS NULL)"];
$params = [];
$types  = "";

if (!empty($department)) {
    $where[]  = "p.dep_id = ?";
    $params[] = $department;
    $types   .= "s";
}

if ($year !== '') {
    $where[]  = "p.year = ?";
    $params[] = $year;
    $types   .= "i";
}

if (!empty($_GET['q'])) {
    $q = "%{$_GET['q']}%";
    $where[]  = "(p.title LIKE ? OR p.synopsis LIKE ? OR t.name LIKE ?)";
    array_push($params, $q, $q, $q);
    $types .= "sss";
}

$supervisor = $_GET['supervisor'] ?? '';
if ($supervisor !== '') {
    $where[]  = "ps.supervisor_id = ?";
    $params[] = (int)$supervisor;
    $types   .= "i";
}

$tag = $_GET['tag'] ?? '';
if ($tag !== '') {
    $where[]  = "pt.tag_id = ?";
    $params[] = (int)$tag;
    $types   .= "i";
}

// ============================
// Sorting
// ============================
$sortMap = [
    'year_asc'   => 'p.year ASC',
    'title_asc'  => 'p.title ASC',
    'title_desc' => 'p.title DESC',
    'dep_asc'    => 'd.dep_name ASC',
    'dep_desc'   => 'd.dep_name DESC',
];
$orderBy = $sortMap[$sort] ?? 'p.year DESC';

// ============================
// Main SQL
// ============================
$sql = "
SELECT 
    p.id,
    p.title,
    p.synopsis,
    p.year,
    p.file_path,
    d.dep_name,
    GROUP_CONCAT(DISTINCT CONCAT(pm.student_name,' (',pm.index_number,')')) AS members,
    GROUP_CONCAT(DISTINCT CONCAT(s.first_name,' ',s.last_name)) AS supervisors,
    GROUP_CONCAT(DISTINCT t.name) AS tags
FROM projects p
LEFT JOIN departments d ON p.dep_id = d.dep_id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN project_supervisors ps ON p.id = ps.project_id
LEFT JOIN supervisors s ON ps.supervisor_id = s.id
LEFT JOIN project_tags pt ON p.id = pt.project_id
LEFT JOIN tags t ON pt.tag_id = t.id
" . (!empty($where) ? "WHERE " . implode(" AND ", $where) : "") . "
GROUP BY p.id
ORDER BY $orderBy
";

// ============================
// Prepare & Execute
// ============================
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// ============================
// Output HTML rows
// ============================
$i = 1;
$hasRows = false;
while ($p = $result->fetch_assoc()):
    $hasRows = true;
?>
<tr>
  <td><?= $i++ ?></td>
  <td><?= htmlspecialchars($p['title']) ?></td>
  <td><?= htmlspecialchars($p['synopsis']) ?></td>
  <td><?= $p['year'] ?></td>
  <td><?= htmlspecialchars($p['dep_name']) ?></td>
  <td><?= $p['members'] ?: '—' ?></td>
  <td><?= $p['supervisors'] ?: '—' ?></td>
  <td><?= $p['tags'] ?: '—' ?></td>
  <td>
    <button class="btn btn-sm btn-primary"
      data-bs-toggle="modal"
      data-bs-target="#viewDetailsModal"
      data-project-id="<?= $p['id'] ?>"
      data-title="<?= htmlspecialchars($p['title']) ?>"
      data-description="<?= htmlspecialchars($p['synopsis']) ?>"
      data-year="<?= $p['year'] ?>"
      data-members="<?= htmlspecialchars($p['members']) ?>"
      data-supervisors="<?= htmlspecialchars($p['supervisors']) ?>"
      data-tags="<?= htmlspecialchars($p['tags']) ?>"
      data-file="<?= htmlspecialchars($p['file_path']) ?>">
      View
    </button>
  </td>
</tr>
<?php endwhile;

if (!$hasRows): ?>
<tr><td colspan="9" class="text-center">No projects found</td></tr>
<?php endif; ?>
