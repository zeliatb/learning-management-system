<?php
session_start();
require_once 'config/db_config.php';  // Database connection

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = $_POST['g-recaptcha-response'];
    $secretKey = "6LfRz_wrAAAAALoh60h25ZptprrQ4nJwVm3AuH6K";
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}");
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success) {
        // Collect POST data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare SQL to fetch student by email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verify email
            if ($row['email_verified'] == 0) {
                echo "<script>alert('Please verify your email before logging in.'); window.location='verify_email.php';</script>";
                exit;
            }

            // Check if user is active
            if ($row['active'] == 0) {
                echo "<script>alert('Account suspended. Contact admin.'); window.location='login.html';</script>";
                exit;
            }

            // Verify password
            if (password_verify($password, $row['password'])) {
                // Create session and redirect to dashboard
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['loggedin'] = true;

                // Check if the user is student or admin
                if ($row['role'] == 'student') {
                    // Redirect to student dashboard
                    header("Location: dashboard.php");
                    exit;

                } else if ($row['role'] == 'admin') {
                    // Redirect to admin dashboard
                    header("Location: admin-dashboard.php");
                    exit;
                }

            }   else {
                echo "<script>alert('Invalid password'); window.location='login.html';</script>";
            }
        }   else {
                echo "<script>alert('Email not found'); window.location='login.html';</script>";
        }
    
    } else {
        // CAPTCHA failed
        echo "<script>alert('Please verify you are not a robot'); window.location='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>