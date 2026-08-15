<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login/");
    exit();
}

include "../datacon.php";
include "../csrf.php";
include "../audit_log.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $dep_id = trim($_POST['dep_id']);
    $dep_name = trim($_POST['dep_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (empty($dep_id) || empty($dep_name) || empty($username) || empty($email)) {
        die("All fields are required.");
    }

    // ✅ Check for duplicates (case-insensitive)
    $checkQuery = $conn->prepare("
        SELECT * FROM departments 
        WHERE LOWER(dep_id) = LOWER(?) 
           OR LOWER(dep_name) = LOWER(?) 
           OR LOWER(email) = LOWER(?)
    ");
    $checkQuery->bind_param("sss", $dep_id, $dep_name, $email);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Department ID, Name, or Email already exists!'); window.location.href='index.php';</script>";
        exit();
    }

    // Generate cryptographically secure random password (16 chars)
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $plainPassword = '';
    for ($i = 0; $i < 16; $i++) {
        $plainPassword .= $chars[random_int(0, strlen($chars) - 1)];
    }

    // ✅ Hash password using bcrypt
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    // ✅ Insert into departments table
    $stmt = $conn->prepare("INSERT INTO departments (dep_id, dep_name, username, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $dep_id, $dep_name, $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        audit_log($conn, 'department_created', "Created department: {$dep_name} (ID: {$dep_id}, user: {$username})");
        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'isabdulaisaiku@gmail.com';
            $mail->Password = 'twkurtspdegwanpu'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('isabdulaisaiku@gmail.com', 'Project Finder Admin');
            $mail->addAddress($email, $dep_name);

            $mail->isHTML(true);
            $mail->Subject = 'Your Department Account Details For RMU Project Finder';
            $mail->Body = "
                <h3>Department Account Created</h3>
                <p>Hello <b>{$dep_name}</b>,</p>
                <p>Your department account has been created. Here are your login details:</p>
                <p><b>Username:</b> {$username}<br>
                <b>Password:</b> {$plainPassword}</p>
                <p>Please change your password after logging in.</p>
                <hr>
                <p>Regards, Project Finder System</p>
            ";

            $mail->send();

            echo "<script>alert('Department added and email sent successfully!'); window.location.href='index.php';</script>";
        } catch (Exception $e) {
            error_log("PHPMailer error (add_department): " . $mail->ErrorInfo);
            echo "<script>alert('Department added, but email could not be sent. Please contact the administrator.'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Error adding department.'); window.location.href='index.php';</script>";
    }
}
?>
