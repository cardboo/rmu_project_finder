<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || !isset($_SESSION['dep_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include "../datacon.php";

$dep_id = $_SESSION['dep_id'];

$stmt = $conn->prepare("SELECT id, first_name, last_name FROM supervisors WHERE dep_id = ?");
$stmt->bind_param("s", $dep_id);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) {
    $rows[] = $r;
}

echo json_encode($rows);
?>
