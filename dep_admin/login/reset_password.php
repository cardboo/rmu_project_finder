<?php
session_start();
require '../datacon.php';

$message = '';
$messageType = '';
$validToken = false;
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (empty($token)) {
    $message = 'Invalid or missing reset token.';
    $messageType = 'danger';
} else {
    // Verify token
    $stmt = $conn->prepare("SELECT id, user_id FROM password_resets WHERE token = ? AND user_type = 'department' AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $validToken = true;
        $resetRow = $result->fetch_assoc();
    } else {
        $message = 'This reset link has expired or is invalid. Please request a new one.';
        $messageType = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters long.';
        $messageType = 'danger';
    } elseif ($password !== $confirmPassword) {
        $message = 'Passwords do not match.';
        $messageType = 'danger';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Update department password
        $upd = $conn->prepare("UPDATE departments SET password = ? WHERE id = ?");
        $upd->bind_param("si", $hashedPassword, $resetRow['user_id']);
        $upd->execute();
        $upd->close();

        // Mark token as used
        $markUsed = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $markUsed->bind_param("i", $resetRow['id']);
        $markUsed->execute();
        $markUsed->close();

        $message = 'Password has been reset successfully. You can now login.';
        $messageType = 'success';
        $validToken = false; // Hide the form
    }
}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - Department</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/rmu.jpg" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <img src="../assets/images/logos/rmu.jpg" width="120" height="120" alt="" style="max-width:100%;height:auto;">
                <h2>RMU PROJECT FINDER</h2>
                <p class="text-center">Reset Department Password</p>

                <?php if ($message): ?>
                  <div class="alert alert-<?= $messageType ?> py-2"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <?php if ($validToken): ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8"
                               placeholder="Enter new password (min 8 chars)">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8"
                               placeholder="Confirm new password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Reset Password</button>
                </form>
                <?php endif; ?>

                <div class="text-center">
                    <a href="index.php" class="text-primary fw-bold">Back to Login</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
