<?php
/**
 * Admin Dashboard Controller
 * 
 * Handles business logic for the admin dashboard page.
 * This file retrieves data and passes it to the view template.
 * 
 * Path: modules/admin/dashboard/dashboard.php
 */

session_start();

// Load core files
require_once dirname(__DIR__, 3) . '/app/core/middleware.php';
require_once dirname(__DIR__, 3) . '/app/core/config.php';

// Require admin role - will redirect if not authorized
requireAdmin();

// Get username from session
$username = $_SESSION['username'];

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Total projects
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects");
if (!$totalQuery) {
  die("Prepare failed: " . $conn->error);
}
$totalQuery->execute();
$totalProjects = $totalQuery->get_result()->fetch_assoc()['total'];

// Total departments
$deptQuery = $conn->prepare("SELECT COUNT(*) AS total FROM departments");
if (!$deptQuery) {
  die("Prepare failed: " . $conn->error);
}
$deptQuery->execute();
$totalDepartments = $deptQuery->get_result()->fetch_assoc()['total'];

// Get the view file
$viewFile = dirname(__FILE__) . '/dashboard.view.php';
if (!file_exists($viewFile)) {
  die("View file not found: " . $viewFile);
}

// Include the view (variables are now available)
require_once $viewFile;
?>
