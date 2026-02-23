<?php
session_start();

if (!isset($_SESSION['username'], $_SESSION['dep_id'])) {
    header("Location: ../dashboard/");
    exit();
}

require "../../app/core/config.php";
require "../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

$dep_id = $_SESSION['dep_id'];

if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== 0) {
    echo "<script>alert('Please upload a valid Excel file'); window.location.href='index.php';</script>";
    exit();
}

$fileTmpPath = $_FILES['excel_file']['tmp_name'];
$fileName = $_FILES['excel_file']['name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

if (!in_array($fileExt, ['xls', 'xlsx'])) {
    echo "<script>alert('Invalid file type. Upload .xls or .xlsx only'); window.location.href='index.php';</script>";
    exit();
}

try {
    $spreadsheet = IOFactory::load($fileTmpPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    if (count($rows) <= 1) {
        throw new Exception("Excel file is empty");
    }

    // Prepare statements
    $checkStmt = $conn->prepare(
        "SELECT id FROM supervisors WHERE email = ? AND dep_id = ?"
    );

    $insertStmt = $conn->prepare(
        "INSERT INTO supervisors (first_name, last_name, email, dep_id)
         VALUES (?, ?, ?, ?)"
    );

    $successCount = 0;
    $skippedCount = 0;

    // Skip header row
    for ($i = 1; $i < count($rows); $i++) {
        $first_name = trim($rows[$i][0] ?? '');
        $last_name  = trim($rows[$i][1] ?? '');
        $email      = trim($rows[$i][2] ?? '');
        $status     = strtolower(trim($rows[$i][3] ?? 'active'));

        if ($first_name === '' || $last_name === '' || $email === '') {
            $skippedCount++;
            continue;
        }

        if (!in_array($status, ['active', 'archived'])) {
            $status = 'active';
        }

        // Check duplicate
        $checkStmt->bind_param("si", $email, $dep_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $skippedCount++;
            continue;
        }

        // Insert
        $insertStmt->bind_param(
            "sssi",
            $first_name,
            $last_name,
            $email,
            $dep_id
        );

        if ($insertStmt->execute()) {
            $successCount++;
        }
    }

    echo "<script>
        alert('Upload complete! Added: {$successCount}, Skipped: {$skippedCount}');
        window.location.href='index.php';
    </script>";

} catch (Exception $e) {
    echo "<script>
        alert('Excel upload failed: {$e->getMessage()}');
        window.location.href='index.php';
    </script>";
}
