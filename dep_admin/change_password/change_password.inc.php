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

    $username = $_SESSION['username'];
    $dep_id = $_SESSION['dep_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header("Location: index.php?error=" . urlencode("All fields are required."));
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("Location: index.php?error=" . urlencode("New passwords do not match."));
        exit();
    }

    if (strlen($new_password) < 8) {
        header("Location: index.php?error=" . urlencode("New password must be at least 8 characters."));
        exit();
    }

    // Fetch current password hash
    $stmt = $conn->prepare("SELECT password FROM departments WHERE dep_id = ? AND username = ?");
    $stmt->bind_param("ss", $dep_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: index.php?error=" . urlencode("Account not found."));
        exit();
    }

    $row = $result->fetch_assoc();

    // Verify current password
    if (!password_verify($current_password, $row['password'])) {
        header("Location: index.php?error=" . urlencode("Current password is incorrect."));
        exit();
    }

    // Update password
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE departments SET password = ? WHERE dep_id = ? AND username = ?");
    $update->bind_param("sss", $hashed, $dep_id, $username);

    if ($update->execute()) {
        audit_log($conn, 'password_changed', "Department admin changed their password");
        header("Location: index.php?success=" . urlencode("Password updated successfully."));
    } else {
        header("Location: index.php?error=" . urlencode("Failed to update password. Please try again."));
    }
    exit();
}

header("Location: index.php");
exit();
?>
