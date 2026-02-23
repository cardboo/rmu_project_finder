<?php
session_start();
require '../datacon.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM departments WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Success - save session data
            $_SESSION['username'] = $row['username'];
            $_SESSION['dep_id'] = $row['dep_id'];
            $_SESSION['dep_name'] = $row['dep_name'];

            // Alert success and redirect
            echo "<script>
                    alert('Login successful! Redirecting to dashboard...');
                    window.location.href = '../dashboard/';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Incorrect password.');
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>
                alert('Username not found.');
                window.history.back();
              </script>";
    }
}
?>
