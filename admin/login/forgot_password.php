<?php
session_start();
require '../datacon.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'danger';
    } else {
        // Look up admin by email
        $stmt = $conn->prepare("SELECT t_id, username FROM admin_logs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Generate secure token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Delete any existing tokens for this user
            $del = $conn->prepare("DELETE FROM password_resets WHERE user_type = 'admin' AND user_id = ?");
            $del->bind_param("i", $admin['t_id']);
            $del->execute();
            $del->close();

            // Insert new token
            $ins = $conn->prepare("INSERT INTO password_resets (user_type, user_id, token, expires_at) VALUES ('admin', ?, ?, ?)");
            $ins->bind_param("iss", $admin['t_id'], $token, $expires);
            $ins->execute();
            $ins->close();

            // Build reset URL
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $resetUrl = "{$protocol}://{$host}" . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token={$token}";

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'isabdulaisaiku@gmail.com';
                $mail->Password = 'twkurtspdegwanpu';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('isabdulaisaiku@gmail.com', 'RMU Project Finder');
                $mail->addAddress($email, $admin['username']);

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset - RMU Project Finder Admin';
                $mail->Body = "
                    <h3>Password Reset Request</h3>
                    <p>Hello <b>{$admin['username']}</b>,</p>
                    <p>We received a request to reset your admin password.</p>
                    <p>Click the link below to reset your password (valid for 1 hour):</p>
                    <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
                    <p>If you did not request this, please ignore this email.</p>
                    <hr>
                    <p>RMU Project Finder System</p>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("PHPMailer error (admin forgot_password): " . $e->getMessage());
            }
        }

        // Always show success message (don't reveal if email exists)
        $message = 'If an account with that email exists, a password reset link has been sent.';
        $messageType = 'success';
    }
}
?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password - Admin</title>
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
                <img src="../assets/images/logos/rmu.jpg" width="120" height="120" alt="">
                <h2>RMU PROJECT FINDER</h2>
                <p class="text-center">Admin Password Reset</p>

                <?php if ($message): ?>
                  <div class="alert alert-<?= $messageType ?> py-2"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               placeholder="Enter your admin email">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Send Reset Link</button>
                    <div class="text-center">
                        <a href="index.php" class="text-primary fw-bold">Back to Login</a>
                    </div>
                </form>
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
