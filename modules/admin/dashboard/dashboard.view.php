<?php
/**
 * Admin Dashboard View
 * 
 * Pure HTML/display template for the admin dashboard.
 * Data is provided by the controller in variables like $totalProjects, $totalDepartments, etc.
 * 
 * Path: modules/admin/dashboard/dashboard.view.php
 */
?>

<div class="row">
  <div class="col-12">
    <h1 class="mb-4">Dashboard</h1>
  </div>
</div>

<div class="row">
  <!-- Total Projects Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-primary">
      <div class="card-body py-5">
        <h5 class="card-title">Total Projects</h5>
        <p class="card-text fs-3 fw-bold"><?php echo htmlspecialchars($totalProjects); ?></p>
      </div>
    </div>
  </div>

  <!-- Total Departments Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-secondary">
      <div class="card-body py-5">
        <h5 class="card-title">Departments</h5>
        <p class="card-text fs-3 fw-bold"><?php echo htmlspecialchars($totalDepartments); ?></p>
      </div>
    </div>
  </div>

  <!-- Quick Links Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-success">
      <div class="card-body py-5">
        <h5 class="card-title">System</h5>
        <p class="card-text">RMU Project Finder</p>
      </div>
    </div>
  </div>
</div>

<!-- Recent Projects Section -->
<div class="row mt-5">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Recent Projects</h5>
      </div>
      <div class="card-body">
        <?php if (!empty($recentProjects)): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Project Title</th>
                  <th>Department</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentProjects as $project): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($project['title']); ?></td>
                    <td><?php echo htmlspecialchars($project['department_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(date('M d, Y', strtotime($project['created_at']))); ?></td>
                    <td>
                      <a href="../view_projects/?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-info">
                        View
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="text-muted">No projects found yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
