<?php
require '../datacon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    $sql = "UPDATE departments SET is_archived = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
          echo "<script>alert('Department Archived successfully'); window.location.href='index.php';</script>";
        exit();
    } else {
          echo "<script>alert('Failed to Archive Department'); window.location.href='index.php';</script>";
        exit();
    }
}
