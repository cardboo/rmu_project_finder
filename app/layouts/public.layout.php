<?php
/**
 * Public Portal Layout Template
 * 
 * This layout wraps public pages with a basic header and footer.
 * 
 * Usage in a controller:
 * <?php
 * $pageTitle = "Project Search";
 * $viewFile = __DIR__ . '/search.view.php';
 * require_once '../../../app/layouts/public.layout.php';
 * ?>
 */

// Set default values if not provided
$pageTitle = $pageTitle ?? 'RMU Project Finder';
$basePath = $basePath ?? '/';

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
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical">
    
    <!--  Main wrapper -->
    <div class="body-wrapper">
      
      <!-- Navbar (Simple for public) -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <div class="container-fluid">
            <a class="navbar-brand" href="/">
              <img src="<?php echo $basePath; ?>assets/images/logos/RMU_logo.png" height="60" alt="RMU Logo" />
            </a>
            <span class="navbar-text ms-auto">
              RMU Project Finder
            </span>
          </div>
        </nav>
      </header>
      
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
