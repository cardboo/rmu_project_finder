<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Unauthorized or missing ID']);
    exit;
}

include "../datacon.php";

$project_id = intval($_GET['id']);
$dep_id = $_SESSION['dep_id']; // Example: "Dep 001"

// ================================
// 1. Fetch MAIN PROJECT DETAILS
// ================================
$stmt = $conn->prepare("
    SELECT title, synopsis, year, status, file_path
    FROM projects
    WHERE id = ? AND dep_id = ?
");
$stmt->bind_param("is", $project_id, $dep_id);  // dep_id is VARCHAR
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Project not found']);
    exit;
}

$project = $result->fetch_assoc();
$stmt->close();

// ================================
// 2. Fetch SUPERVISORS (with full name)
// ================================
$sup_stmt = $conn->prepare("
    SELECT s.id, s.first_name, s.last_name
    FROM project_supervisors ps
    JOIN supervisors s ON s.id = ps.supervisor_id
    WHERE ps.project_id = ?
");
$sup_stmt->bind_param("i", $project_id);
$sup_stmt->execute();
$sup_res = $sup_stmt->get_result();

$supervisors = [];
while ($row = $sup_res->fetch_assoc()) {
    $supervisors[] = [
        'id' => $row['id'],
        'name' => $row['first_name'] . ' ' . $row['last_name']
    ];
}
$sup_stmt->close();

// ================================
// 3. Fetch MEMBERS
// ================================
$member_stmt = $conn->prepare("
    SELECT student_name, index_number 
    FROM project_members 
    WHERE project_id = ?
");
$member_stmt->bind_param("i", $project_id);
$member_stmt->execute();
$member_res = $member_stmt->get_result();

$members = [];
while ($row = $member_res->fetch_assoc()) {
    $members[] = "{$row['student_name']} ({$row['index_number']})";
}
$member_stmt->close();

// ================================
// 4. Fetch TAGS
// ================================
$tag_stmt = $conn->prepare("
    SELECT t.name
    FROM project_tags pt
    JOIN tags t ON t.id = pt.tag_id
    WHERE pt.project_id = ?
");
$tag_stmt->bind_param("i", $project_id);
$tag_stmt->execute();
$tag_res = $tag_stmt->get_result();

$tags = [];
while ($row = $tag_res->fetch_assoc()) {
    $tags[] = $row['name'];
}
$tag_stmt->close();

$conn->close();

// ================================
// OUTPUT JSON
// ================================
echo json_encode([
    'title'        => $project['title'],
    'synopsis'     => $project['synopsis'],
    'year'         => $project['year'],
    'status'       => $project['status'],
    'file_path'    => $project['file_path'],   // FIXED
    'supervisors'  => $supervisors,            // FIXED
    'members'      => $members,
    'tags'         => $tags
]);
?>
