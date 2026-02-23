<?php
/**
 * CSS Includes Partial
 * Shared CSS includes for all pages
 * 
 * Usage: <?php include 'app/partials/styles.php'; ?>
 */

if (!isset($basePath)) {
    $basePath = '/';
}
?>

<link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/styles.min.css" />
