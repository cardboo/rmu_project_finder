<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login/");
    exit();
}

require '../datacon.php';
require '../csrf.php';
require '../audit_log.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = intval($_POST['id']);
    $dep_id = $_SESSION['dep_id'];

    // Ensure the project belongs to this department
    $check = $conn->prepare("SELECT id, title FROM projects WHERE id = ? AND dep_id = ?");
    $check->bind_param("is", $id, $dep_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Project not found or access denied.'); window.location.href='index.php';</script>";
        exit();
    }

    $project = $result->fetch_assoc();

    $stmt = $conn->prepare("UPDATE projects SET is_archived = 1 WHERE id = ? AND dep_id = ?");
    $stmt->bind_param("is", $id, $dep_id);

    if ($stmt->execute()) {
        audit_log($conn, 'project_archived', "Archived project: {$project['title']} (ID: {$id})");
        echo "<script>alert('Project archived successfully.'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Failed to archive project.'); window.location.href='index.php';</script>";
    }
}
?>
