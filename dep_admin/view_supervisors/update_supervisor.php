<?php
require '../../app/core/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

        $sql = "UPDATE supervisors SET  first_name = ?, last_name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi",  $first_name, $last_name, $email, $id);

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
