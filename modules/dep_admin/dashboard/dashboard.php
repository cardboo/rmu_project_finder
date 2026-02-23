<?php
/**
 * Department Admin Dashboard Controller
 * 
 * Handles business logic for the department admin dashboard page.
 * This file retrieves department-specific data and passes it to the view.
 * 
 * Path: modules/dep_admin/dashboard/dashboard.php
 */

session_start();

// Load core files
require_once dirname(__DIR__, 3) . '/app/core/config.php';

// Check if user is logged in and is department admin
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'dep_admin') {
    header("Location: ../../dep_admin/login/");
    exit;
}

// Get session data
$username = $_SESSION['username'];
$dep_id = $_SESSION['dep_id'];
$dep_name = $_SESSION['dep_name'];

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Total projects for this department
$totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects WHERE dep_id = ?");
if (!$totalQuery) {
  die("Prepare failed: " . $conn->error);
}
$totalQuery->bind_param("s", $dep_id);
$totalQuery->execute();
$totalProjects = $totalQuery->get_result()->fetch_assoc()['total'];

// Get the view file
$viewFile = dirname(__FILE__) . '/dashboard.view.php';
if (!file_exists($viewFile)) {
  die("View file not found: " . $viewFile);
}

// Include the view (variables are now available)
require_once $viewFile;
?>

