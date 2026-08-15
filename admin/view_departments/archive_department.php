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

    $sql = "UPDATE departments SET is_archived = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
          audit_log($conn, 'department_archived', "Archived department ID: {$id}");
          echo "<script>alert('Department Archived successfully'); window.location.href='index.php';</script>";
        exit();
    } else {
          echo "<script>alert('Failed to Archive Department'); window.location.href='index.php';</script>";
        exit();
    }
}
