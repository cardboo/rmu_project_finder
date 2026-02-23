<?php
<?php
/**
 * BACKWARD COMPATIBILITY LAYER
 * This file now delegates to the new centralized config system.
 * It's kept here to ensure existing includes continue to work.
 */

// Determine the correct path to app/core/config.php
$appRoot = dirname(dirname(__FILE__));

// Include the new centralized config
require_once $appRoot . '/app/core/config.php';

// The $conn variable is now available from the new config
?>

