<?php
header('Content-Type: application/json');
require 'datacon.php'; // adjust the path if needed

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if ($query === '') {
    echo json_encode([]);
    exit;
}

// Split query into individual words
$keywords = preg_split('/\s+/', $query);

// Build dynamic WHERE clause
$whereClauses = [];
$params = [];
$types = '';

// For each keyword, add OR conditions inside an AND group
foreach ($keywords as $keyword) {
    $keyword = "%$keyword%";
    $whereClauses[] = "(p.title LIKE ? OR pm.student_name LIKE ? OR t.name LIKE ? OR d.dep_name LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;
    $types .= 'ssssss';
}

$whereSql = implode(' AND ', $whereClauses);

$sql = "
SELECT 
  p.id,
  p.title,
  p.synopsis,
  p.year,
  d.dep_name,
  p.file_path,
  GROUP_CONCAT(DISTINCT pm.student_name SEPARATOR ', ') AS student_names,
  GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS tags,
  GROUP_CONCAT(DISTINCT CONCAT(s.first_name,' ',s.last_name) SEPARATOR ', ') AS supervisors
FROM projects p
JOIN departments d ON p.dep_id = d.dep_id
LEFT JOIN project_members pm ON p.id = pm.project_id
LEFT JOIN project_tags pt ON p.id = pt.project_id
LEFT JOIN tags t ON pt.tag_id = t.id
LEFT JOIN project_supervisors ps ON p.id = ps.project_id
LEFT JOIN supervisors s ON ps.supervisor_id = s.id
WHERE $whereSql
GROUP BY p.id
ORDER BY p.year DESC
LIMIT 30
";

$stmt = $conn->prepare($sql);

// Bind params dynamically
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);
