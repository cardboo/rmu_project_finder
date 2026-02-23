<?php
require '../../app/core/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id       = intval($_POST['id']);
    $dep_id   = trim($_POST['dep_id']);
    $dep_name = trim($_POST['dep_name']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);

    if (empty($dep_id) || empty($dep_name) || empty($username) || empty($email)) {
        echo "<script>alert('All fields are required'); window.location.href='index.php';</script>";
        exit();
    }

    /** 1️⃣ Fetch OLD department data */
    $old_dep_id = '';
    $old_email  = '';

    $getOld = $conn->prepare(
        "SELECT dep_id, email FROM departments WHERE id = ?"
    );
    $getOld->bind_param("i", $id);
    $getOld->execute();
    $getOld->bind_result($old_dep_id, $old_email);
    $getOld->fetch();
    $getOld->close();

    /** 2️⃣ Detect email change */
    $emailChanged = strtolower($email) !== strtolower($old_email);

    /** 3️⃣ If email changed → generate new password */
    if ($emailChanged) {
        $plainPassword = substr(
            str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*"),
            0,
            8
        );
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
    }

    /** 4️⃣ Begin transaction */
    $conn->begin_transaction();

    try {
        /** 5️⃣ Update departments table */
        if ($emailChanged) {
            $sql = "
                UPDATE departments 
                SET dep_id = ?, dep_name = ?, username = ?, email = ?, password = ?
                WHERE id = ?
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssssi",
                $dep_id,
                $dep_name,
                $username,
                $email,
                $hashedPassword,
                $id
            );
        } else {
            $sql = "
                UPDATE departments 
                SET dep_id = ?, dep_name = ?, username = ?, email = ?
                WHERE id = ?
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssi",
                $dep_id,
                $dep_name,
                $username,
                $email,
                $id
            );
        }

        $stmt->execute();

        /** 6️⃣ Update related projects */
        $sql2 = "UPDATE projects SET dep_id = ? WHERE dep_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $dep_id, $old_dep_id);
        $stmt2->execute();

        /** 7️⃣ Commit DB changes */
        $conn->commit();

        /** 8️⃣ Send email ONLY if email changed */
        if ($emailChanged) {
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'isabdulaisaiku@gmail.com';
                $mail->Password   = 'twkurtspdegwanpu'; // app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('isabdulaisaiku@gmail.com', 'Project Finder Admin');
                $mail->addAddress($email, $dep_name);

                $mail->isHTML(true);
                $mail->Subject = 'Department Account Updated – New Login Details';
                $mail->Body = "
                    <h3>Department Account Updated</h3>
                    <p>Hello <b>{$dep_name}</b>,</p>

                    <p>Your department email has been updated.  
                    For security reasons, your password has been reset.</p>

                    <p><b>Username:</b> {$username}<br>
                    <b>New Password:</b> {$plainPassword}</p>

                    <p>Please log in and change your password immediately.</p>

                    <hr>
                    <p>Project Finder System</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                // Email failure shouldn't rollback DB changes
            }
        }

        echo "<script>alert('Department updated successfully'); window.location.href='index.php';</script>";
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Failed to update department'); window.location.href='index.php';</script>";
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
