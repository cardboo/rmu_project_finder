<?php
<?php
/**
 * BACKWARD COMPATIBILITY LAYER
 * This file now delegates to the new centralized config system.
 * It's kept here to ensure existing includes continue to work.
 */

// Determine the correct path to app/core/config.php based on include location
if (!defined('APP_ROOT')) {
    $currentDir = dirname(__FILE__);
    define('APP_ROOT', $currentDir);
}

// Include the new centralized config
require_once APP_ROOT . '/app/core/config.php';

// The $conn variable is now available from the new config
?>

