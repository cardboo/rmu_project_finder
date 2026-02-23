<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['dep_id'])) {
    header("Location: ../login/");
    exit();
}

include "../datacon.php";
include "../csrf.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = intval($_POST['supervisor_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);

    if (!empty($first_name) && !empty($last_name) && !empty($email)) {
        // Check for duplicate email (excluding current supervisor)
        $checkEmail = $conn->prepare("SELECT id FROM supervisors WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $email, $id);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            echo "<script>alert('Email already exists'); window.location.href='index.php';</script>";
            exit();
        }

        $dep_id = $_SESSION['dep_id'];
        $sql = "UPDATE supervisors SET first_name = ?, last_name = ?, email = ? WHERE id = ? AND dep_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssis", $first_name, $last_name, $email, $id, $dep_id);

        if ($stmt->execute()) {
            echo "<script>alert('Supervisor updated successfully'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Failed to update supervisor'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('All fields are required'); window.location.href='index.php';</script>";
    }
}
?>
