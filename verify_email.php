<?php
require_once 'config/db_config.php'; // Database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM students WHERE token = ? LIMIT 1";

    $stmt = $conn->prepare($sql); // Create prepared statement
    $stmt->bind_param("i", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email found, update verification status in database
        $update = $conn->prepare("UPDATE students SET email_verified = 1, token = NULL WHERE token = ?");
        $update->bind_param("i", $token);
        $update->execute();

        echo "<script>alert('Email verified successfully! You can now log in.'); window.location='login.html';</script>";
    } else {
        echo "<script>alert('Invalid or expired verification link.'); window.location='index.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // Token not present, show resend form
    echo "<h3>Please check your email for the verification link.</h3>
    <form action='resend_verification.php' method='POST'>
        <input type='email' name='email' placeholder='Enter your email to resend link' required>
        <button type='submit'>Resend Verification Email</button>
    </form>";
}
?>