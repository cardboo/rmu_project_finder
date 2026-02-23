<?php
/**
 * TEMPLATE: New Controller File
 * 
 * Copy this template when creating new admin pages.
 * Replace "mypage" with your actual page name.
 * 
 * Location: modules/admin/mypage/mypage.php
 */

session_start();

// Load core files for auth and database
require_once '../../../app/core/middleware.php';
require_once '../../../app/core/config.php';

// Enforce admin role - redirects to login if not authorized
requireAdmin();

// Get user info from session
$username = $_SESSION['username'] ?? 'Administrator';

// ============================================
// BUSINESS LOGIC - All queries and processing
// ============================================

try {
    // Example: Fetch data from database
    // $query = $conn->prepare("SELECT * FROM projects WHERE department_id = ?");
    // $query->bind_param("i", $departmentId);
    // $query->execute();
    // $result = $query->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // For now, use sample data
    $sampleData = [];
    
} catch (Exception $e) {
    error_log("Error fetching data: " . $e->getMessage());
    $sampleData = [];
    $error = "Failed to load page data.";
}

// ============================================
// PAGE SETUP - Tell layout what to render
// ============================================

$pageTitle = "My Admin Page";  // Shows in browser tab
$basePath = "../";             // Path to assets (change if nested deeper)
$viewFile = __DIR__ . '/mypage.view.php';  // Which view to display

// ============================================
// LOAD LAYOUT - Renders HTML structure
// ============================================
// This includes: sidebar, navbar, footer, and loads $viewFile as content
require_once '../../../app/layouts/admin.layout.php';

?>
