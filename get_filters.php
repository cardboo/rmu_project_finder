<?php
header('Content-Type: application/json');
require 'datacon.php';

// Get all departments
$deps = [];
$r = $conn->query("SELECT dep_id, dep_name FROM departments WHERE is_archived = 0 ORDER BY dep_name ASC");
while ($row = $r->fetch_assoc()) {
    $deps[] = $row;
}

// Get all tags with project counts (exclude archived)
$tags = [];
$r = $conn->query("
    SELECT t.id, t.name, COUNT(pt.project_id) AS project_count
    FROM tags t
    JOIN project_tags pt ON t.id = pt.tag_id
    JOIN projects p ON pt.project_id = p.id AND (p.is_archived = 0 OR p.is_archived IS NULL)
    GROUP BY t.id
    ORDER BY project_count DESC, t.name ASC
");
while ($row = $r->fetch_assoc()) {
    $tags[] = $row;
}

// Get year range (exclude archived)
$years = [];
$r = $conn->query("SELECT DISTINCT year FROM projects WHERE is_archived = 0 OR is_archived IS NULL ORDER BY year DESC");
while ($row = $r->fetch_assoc()) {
    $years[] = (int)$row['year'];
}

// Get total project count (exclude archived)
$r = $conn->query("SELECT COUNT(*) AS total FROM projects WHERE is_archived = 0 OR is_archived IS NULL");
$total = $r->fetch_assoc()['total'];

echo json_encode([
    'departments' => $deps,
    'tags' => $tags,
    'years' => $years,
    'total_projects' => (int)$total
]);
