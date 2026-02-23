<?php
/**
 * Admin Portal Layout Template
 * 
 * This layout wraps admin pages with consistent header, sidebar, and footer.
 * 
 * Usage in a controller:
 * <?php
 * $pageTitle = "Dashboard";
 * $basePath = "../";
 * $viewFile = __DIR__ . '/dashboard.view.php';
 * require_once '../../../../app/layouts/admin.layout.php';
 * ?>
 */

require_once __DIR__ . '/../core/auth.php';
require_once __DIR__ . '/../core/middleware.php';

// Ensure user is authenticated as admin
requireAdmin();

// Set default values if not provided
$pageTitle = $pageTitle ?? 'RMU Project Finder - Admin';
$basePath = $basePath ?? '../';

// Verify view file is provided
if (!isset($viewFile) || !file_exists($viewFile)) {
    die('Error: View file not specified or does not exist.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include __DIR__ . '/../partials/head.php'; ?>
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar -->
    <?php include __DIR__ . '/../components/sidebar.php'; ?>
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      
      <!-- Navbar -->
      <?php include __DIR__ . '/../components/navbar.php'; ?>
      
      <!-- Main Content -->
      <main class="container content-container">
        <?php include $viewFile; ?>
      </main>
      
      <!-- Footer -->
      <?php include __DIR__ . '/../components/footer.php'; ?>
      
    </div>
    
  </div>
  
  <!-- Scripts -->
  <?php include __DIR__ . '/../partials/scripts.php'; ?>
</body>
</html>
