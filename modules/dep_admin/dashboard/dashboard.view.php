<?php
/**
 * Department Admin Dashboard View
 * 
 * Pure HTML/display template for the department admin dashboard.
 * 
 * Path: modules/dep_admin/dashboard/dashboard.view.php
 */
?>

<div class="row">
  <div class="col-12">
    <h1 class="mb-4">Department Dashboard</h1>
    <p class="text-muted">Department: <?php echo htmlspecialchars($departmentName); ?></p>
  </div>
</div>

<div class="row">
  <!-- Total Projects Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-primary">
      <div class="card-body py-5">
        <h5 class="card-title">Projects</h5>
        <p class="card-text fs-3 fw-bold"><?php echo htmlspecialchars($totalProjects); ?></p>
        <a href="../view_projects" class="btn btn-sm btn-light">Manage</a>
      </div>
    </div>
  </div>

  <!-- Total Supervisors Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-info">
      <div class="card-body py-5">
        <h5 class="card-title">Supervisors</h5>
        <p class="card-text fs-3 fw-bold"><?php echo htmlspecialchars($totalSupervisors); ?></p>
        <a href="../view_supervisors" class="btn btn-sm btn-light">Manage</a>
      </div>
    </div>
  </div>

  <!-- Quick Actions Card -->
  <div class="col-md-6 col-lg-4 mb-4">
    <div class="card text-white bg-success">
      <div class="card-body py-5">
        <h5 class="card-title">Quick Actions</h5>
        <p class="card-text">
          <a href="../view_projects" class="text-white">View Projects</a><br>
          <a href="../view_supervisors" class="text-white">Manage Supervisors</a>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Recent Projects Section -->
<div class="row mt-5">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Projects</h5>
          <a href="../view_projects" class="btn btn-sm btn-primary">View All</a>
        </div>
      </div>
      <div class="card-body">
        <?php if (!empty($recentProjects)): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Project Title</th>
                  <th>Created</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentProjects as $project): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($project['title']); ?></td>
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
          <p class="text-muted">No projects found. <a href="../view_projects">Add one now</a></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
