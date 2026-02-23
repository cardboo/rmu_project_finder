<?php
session_start();
require '../datacon.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin_logs WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Success - save session data
            $_SESSION['username'] = $row['username'];
            

            // Alert success and redirect
            echo "<script>
                    alert('Login successful! Redirecting to dashboard...');
                    window.location.href = '../dashboard/';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Invalid username or password.');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('Invalid username or password.');
                window.history.back();
              </script>";
    }
}
?>
