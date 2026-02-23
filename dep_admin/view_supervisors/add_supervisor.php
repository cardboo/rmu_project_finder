<?php
include "../datacon.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dep_id = $_POST['dep_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
   

    if (!empty($dep_id) && !empty($first_name) && !empty($last_name) && !empty($email)) {

        // Check for duplicate email
        $checkSql = "SELECT id FROM supervisors WHERE email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='index.php';</script>";
        } else {
            // Insert new supervisor
            $sql = "INSERT INTO supervisors (dep_id, first_name, last_name, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $dep_id, $first_name, $last_name, $email);

            if ($stmt->execute()) {
                echo "<script>alert('Supervisor added successfully'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Failed to add supervisor'); window.location.href='index.php';</script>";
            }
        }

        $checkStmt->close();
    } else {
        echo "<script>alert('All fields are required'); window.location.href='index.php';</script>";
    }
}
?>
