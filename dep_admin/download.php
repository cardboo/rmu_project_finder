<?php
// download.php
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("No file specified.");
}

// Sanitize filename
$file = basename($_GET['file']);

// Correct file path
$filepath = __DIR__ . '/uploads/projects/' . $file;

if (!file_exists($filepath)) {
    die("File not found.");
}

// MIME types
$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
];
$mime = $mimeTypes[$ext] ?? 'application/octet-stream';

// Download headers
header('Content-Description: File Transfer');
header("Content-Type: $mime");
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

readfile($filepath);
exit;
?>
