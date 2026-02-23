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
require_once '../../../app/core/middleware.php';
require_once '../../../app/core/config.php';

// Require admin role - will redirect if not authorized
requireAdmin();

// Get username from session
$username = $_SESSION['username'] ?? 'Administrator';

// Fetch dashboard data
try {
    // Total projects
    $totalQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects");
    $totalQuery->execute();
    $totalProjects = $totalQuery->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Total departments
    $deptQuery = $conn->prepare("SELECT COUNT(*) AS total FROM departments");
    $deptQuery->execute();
    $totalDepartments = $deptQuery->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Recent projects (limit 5)
    $recentQuery = $conn->prepare("
        SELECT p.id, p.title, d.department_name, p.created_at 
        FROM projects p 
        LEFT JOIN departments d ON p.department_id = d.id 
        ORDER BY p.created_at DESC 
        LIMIT 5
    ");
    $recentQuery->execute();
    $recentProjects = $recentQuery->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
    
} catch (Exception $e) {
    error_log("Dashboard query error: " . $e->getMessage());
    $totalProjects = 0;
    $totalDepartments = 0;
    $recentProjects = [];
}

// Set page data
$pageTitle = "Admin Dashboard";
$basePath = "../";
$viewFile = __DIR__ . '/dashboard.view.php';

// Load layout which renders the page
require_once '../../../app/layouts/admin.layout.php';
?>
