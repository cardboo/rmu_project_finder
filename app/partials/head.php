<?php
/**
 * HTML Head Partial
 * Shared head section for admin pages
 * 
 * Usage: <?php include 'app/partials/head.php'; ?>
 */

// Determine the base path dynamically based on current location
if (!isset($basePath)) {
    $basePath = '/';
}

if (!isset($pageTitle)) {
    $pageTitle = 'RMU Project Finder';
}

?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="shortcut icon" type="image/png" href="<?php echo $basePath; ?>assets/images/logos/rmu.jpg" />
<link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/styles.min.css" />

<style>
  .content-container {
    padding-top: 80px;
  }
  
  /* Additional shared styles can go here */
</style>
