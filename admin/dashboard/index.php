<?php
/**
 * LEGACY WRAPPER - Admin Dashboard
 * 
 * This file maintains backward compatibility by delegating to the new module structure.
 * The actual logic and view have been moved to modules/admin/dashboard/
 */

// Delegate to the new module
require_once '../../modules/admin/dashboard/dashboard.php';
?>
