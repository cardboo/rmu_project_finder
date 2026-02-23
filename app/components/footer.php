<?php
/**
 * Footer Component
 * Shared footer for all pages
 * 
 * Usage: <?php include 'app/components/footer.php'; ?>
 */

$currentYear = date('Y');
?>

<footer class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <p class="text-muted mb-0">
          &copy; <?php echo $currentYear; ?> Regent Maritime University. All rights reserved.
        </p>
      </div>
      <div class="col-md-6 text-end">
        <p class="text-muted mb-0">
          RMU Project Finder v1.0
        </p>
      </div>
    </div>
  </div>
</footer>
