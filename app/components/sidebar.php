<?php
/**
 * Sidebar Component
 * Role-aware sidebar that renders different navigation based on user role
 * 
 * Usage: <?php include 'app/components/sidebar.php'; ?>
 * 
 * Requires: User session with 'role' set
 */

require_once __DIR__ . '/../core/auth.php';

$userRole = getUserRole();
$basePath = isset($basePath) ? $basePath : '../';

?>

<aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="<?php echo $basePath; ?>dashboard" class="text-nowrap logo-img">
        <img src="<?php echo $basePath; ?>assets/images/logos/RMU_logo.png" width="180" height="60" alt="RMU Logo" />
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>
    
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <li class="nav-small-cap">
          <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
          <span class="hide-menu">Menu</span>
        </li>

        <!-- Dashboard - Available to all admin roles -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $basePath; ?>dashboard" aria-expanded="false">
            <span>
              <i class="ti ti-layout-dashboard"></i>
            </span>
            <span class="hide-menu">Dashboard</span>
          </a>
        </li>

        <?php if ($userRole === 'admin'): ?>
          <!-- Super Admin Navigation -->
          
          <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $basePath; ?>view_departments" aria-expanded="false">
              <span>
                <i class="ti ti-article"></i>
              </span>
              <span class="hide-menu">Manage Departments</span>
            </a>
          </li>

          <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $basePath; ?>view_projects" aria-expanded="false">
              <span>
                <i class="ti ti-article"></i>
              </span>
              <span class="hide-menu">View All Projects</span>
            </a>
          </li>

        <?php elseif ($userRole === 'dep_admin'): ?>
          <!-- Department Admin Navigation -->
          
          <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $basePath; ?>view_projects" aria-expanded="false">
              <span>
                <i class="ti ti-article"></i>
              </span>
              <span class="hide-menu">Manage Projects</span>
            </a>
          </li>

          <li class="sidebar-item">
            <a class="sidebar-link" href="<?php echo $basePath; ?>view_supervisors" aria-expanded="false">
              <span>
                <i class="ti ti-users"></i>
              </span>
              <span class="hide-menu">Manage Supervisors</span>
            </a>
          </li>

        <?php endif; ?>

        <!-- Divider -->
        <li class="nav-small-cap">
          <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
          <span class="hide-menu">Account</span>
        </li>

        <!-- Logout -->
        <li class="sidebar-item">
          <a class="sidebar-link" href="<?php echo $basePath; ?>logout" aria-expanded="false">
            <span>
              <i class="ti ti-logout"></i>
            </span>
            <span class="hide-menu">Logout</span>
          </a>
        </li>
      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll-->
</aside>
