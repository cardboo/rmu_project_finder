<?php
/**
 * TEMPLATE: New View File
 * 
 * Copy this template when creating new admin page views.
 * This file contains ONLY HTML/display code.
 * Data comes from the controller in variables like $sampleData, $pageTitle, etc.
 * 
 * Location: modules/admin/mypage/mypage.view.php
 */
?>

<div class="row">
  <div class="col-12">
    <h1 class="mb-4">Page Title</h1>
  </div>
</div>

<!-- Example: Card Display -->
<div class="row">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Card Title</h5>
      </div>
      <div class="card-body">
        <p>Card content goes here</p>
      </div>
    </div>
  </div>
</div>

<!-- Example: Table Display -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Data Table</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($sampleData)): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Column 1</th>
                  <th>Column 2</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($sampleData as $item): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($item['field1']); ?></td>
                    <td><?php echo htmlspecialchars($item['field2']); ?></td>
                    <td>
                      <a href="#" class="btn btn-sm btn-info">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="text-muted">No data found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
