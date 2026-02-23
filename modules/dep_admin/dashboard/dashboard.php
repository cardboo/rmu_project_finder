<?php
/**
 * Department Admin Dashboard Controller
 * 
 * Handles business logic for the department admin dashboard page.
 * 
 * Path: modules/dep_admin/dashboard/dashboard.php
 */

session_start();

// Load core files
require_once '../../../app/core/middleware.php';
require_once '../../../app/core/config.php';

// Require department admin role
requireDepartmentAdmin();

// Get user data from session
$username = $_SESSION['username'] ?? 'Administrator';
$departmentId = getUserDepartment();

// Verify department access
requireDepartmentAccess($departmentId);

try {
    // Total projects in this department
    $projectQuery = $conn->prepare("SELECT COUNT(*) AS total FROM projects WHERE department_id = ?");
    $projectQuery->bind_param("i", $departmentId);
    $projectQuery->execute();
    $totalProjects = $projectQuery->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Total supervisors in this department
    $supervisorQuery = $conn->prepare("SELECT COUNT(*) AS total FROM supervisors WHERE department_id = ?");
    $supervisorQuery->bind_param("i", $departmentId);
    $supervisorQuery->execute();
    $totalSupervisors = $supervisorQuery->get_result()->fetch_assoc()['total'] ?? 0;
    
    // Department name
    $deptQuery = $conn->prepare("SELECT department_name FROM departments WHERE id = ?");
    $deptQuery->bind_param("i", $departmentId);
    $deptQuery->execute();
    $departmentName = $deptQuery->get_result()->fetch_assoc()['department_name'] ?? 'Department';
    
    // Recent projects
    $recentQuery = $conn->prepare("
        SELECT id, title, created_at 
        FROM projects 
        WHERE department_id = ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recentQuery->bind_param("i", $departmentId);
    $recentQuery->execute();
    $recentProjects = $recentQuery->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
    
} catch (Exception $e) {
    error_log("Department Dashboard query error: " . $e->getMessage());
    $totalProjects = 0;
    $totalSupervisors = 0;
    $departmentName = 'Department';
    $recentProjects = [];
}

// Set page data
$pageTitle = "Department Dashboard - " . htmlspecialchars($departmentName);
$basePath = "../";
$viewFile = __DIR__ . '/dashboard.view.php';

// Load layout which renders the page
require_once '../../../app/layouts/dep_admin.layout.php';
?>
