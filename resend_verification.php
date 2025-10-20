<?php
require_once 'config/db_config.php'; // Database Connection

$email = $_POST['email'];
$newToken = random_int(100000, 999999);

// Update verification status in database
$sql = "UPDATE students SET token='$newToken' WHERE email='$email'";

// Send verification email
if ($conn->query($sql)) {
    $link = "https://zeliatbraimah.eagletechafrica.com/verify_email.php?token=$newToken";
    $subject = "Email Verification - Nova LMS";
    $message = "Hi, here is your verification code: $newToken\n\nPlease click the link below to verify your email:\n$link\n\nThank you,\nNova LMS";
    $headers = "From: noreply@zeliatbraimah.eagletechafrica.com\r\n";
    mail($email, $subject, $message, $headers);

        echo "<script>alert('Verification link resent successfully. Check your inbox.'); window.location='verify_email.php';</script>";
    } else {
        // Redirect to registration page if email does not exist
        echo "<script>alert('Email not found. Please register again.'); window.location='index.html';</script>";
}
?>