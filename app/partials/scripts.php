<?php
/**
 * JavaScript Includes Partial
 * Shared script includes for admin pages
 * 
 * Usage: <?php include 'app/partials/scripts.php'; ?>
 */

if (!isset($basePath)) {
    $basePath = '/';
}
?>

<!-- jQuery -->
<script src="<?php echo $basePath; ?>assets/libs/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="<?php echo $basePath; ?>assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar Menu -->
<script src="<?php echo $basePath; ?>assets/js/sidebarmenu.js"></script>

<!-- App Core -->
<script src="<?php echo $basePath; ?>assets/js/app.min.js"></script>

<!-- ApexCharts -->
<script src="<?php echo $basePath; ?>assets/libs/apexcharts/dist/apexcharts.min.js"></script>

<!-- SimpleBar -->
<script src="<?php echo $basePath; ?>assets/libs/simplebar/dist/simplebar.js"></script>
