<?php
/**
 * Navbar Component
 * Shared navbar for admin pages
 * 
 * Usage: <?php include 'app/components/navbar.php'; ?>
 * 
 * Requires: User session data
 */

require_once __DIR__ . '/../core/auth.php';

$username = $_SESSION['username'] ?? 'User';
$userRole = getUserRole();
$basePath = isset($basePath) ? $basePath : '../';

?>

<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle pe-0" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="d-block">
            <i class="ti ti-user"></i> <?php echo htmlspecialchars($username); ?>
          </span>
        </a>
        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
          <li>
            <a class="dropdown-item" href="<?php echo $basePath; ?>logout">
              <i class="ti ti-logout"></i> Logout
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</header>
