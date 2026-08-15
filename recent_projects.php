<?php
header('Content-Type: application/json');
require 'datacon.php';

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
WHERE (p.is_archived = 0 OR p.is_archived IS NULL)
GROUP BY p.id
ORDER BY p.year DESC, p.id DESC
LIMIT 12
";

$result = $conn->query($sql);
$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode(['projects' => $projects]);
