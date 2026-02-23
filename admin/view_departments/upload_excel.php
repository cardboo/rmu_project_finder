<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login/");
    exit();
}

include "../datacon.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== 0) {
        die("Invalid file upload.");
    }

    $fileTmpPath = $_FILES['excel_file']['tmp_name'];

    try {
        $spreadsheet = IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Remove header row
        unset($rows[0]);

        $inserted = 0;
        $skipped = 0;

        foreach ($rows as $row) {

            $dep_id   = trim($row[0] ?? '');
            $dep_name = trim($row[1] ?? '');
            $username = trim($row[2] ?? '');
            $email    = trim($row[3] ?? '');

            if (empty($dep_id) || empty($dep_name) || empty($username) || empty($email)) {
                $skipped++;
                continue;
            }

            /** ✅ Check duplicates (case-insensitive) */
            $check = $conn->prepare("
                SELECT id FROM departments 
                WHERE LOWER(dep_id) = LOWER(?) 
                   OR LOWER(dep_name) = LOWER(?) 
                   OR LOWER(email) = LOWER(?)
            ");
            $check->bind_param("sss", $dep_id, $dep_name, $email);
            $check->execute();
            $exists = $check->get_result();

            if ($exists->num_rows > 0) {
                $skipped++;
                continue;
            }

            /** ✅ Generate password */
            $plainPassword = substr(
                str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*"),
                0,
                8
            );

            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            /** ✅ Insert department */
            $stmt = $conn->prepare("
                INSERT INTO departments (dep_id, dep_name, username, email, password)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssss", $dep_id, $dep_name, $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $inserted++;

                /** ✅ Send Email */
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'isabdulaisaiku@gmail.com';
                    $mail->Password = 'twkurtspdegwanpu'; // app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('isabdulaisaiku@gmail.com', 'Project Finder Admin');
                    $mail->addAddress($email, $dep_name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your RMU Project Finder Department Account';
                    $mail->Body = "
                        <h3>Department Account Created</h3>
                        <p>Hello <b>{$dep_name}</b>,</p>
                        <p>Your department account has been created.</p>
                        <p>
                            <b>Username:</b> {$username}<br>
                            <b>Password:</b> {$plainPassword}
                        </p>
                        <p>Please change your password after login.</p>
                        <hr>
                        <p>RMU Project Finder System</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    // Email failure does NOT stop insert
                }
            } else {
                $skipped++;
            }
        }

        echo "
        <script>
            alert('Upload completed: {$inserted} added, {$skipped} skipped.');
            window.location.href = 'index.php';
        </script>
        ";

    } catch (Exception $e) {
        die("Error reading Excel file: " . $e->getMessage());
    }
}
