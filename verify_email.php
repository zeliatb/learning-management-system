<?php
require_once 'config/db_config.php'; // Database connection

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM users WHERE token = ? LIMIT 1";

    $stmt = $conn->prepare($sql); // Create prepared statement
    $stmt->bind_param("i", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email found, update verification status in database
        $update = $conn->prepare("UPDATE users SET email_verified = 1, token = NULL WHERE token = ?");
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
    echo <<<HTML
    <style>
    body {
        background-color: #f3efe7;
    }
    h3 {
        font-size: 25px;
        color: #222;
        margin-bottom: 1.5rem;
    }
    .form-card {
        max-width: 500px;
        margin: 5rem auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        padding: 3rem 2.5rem;
        text-align: center;
    }
    input[type="email"] {
        width: 100%;
        padding: 12px 14px;
        font-size: 14px;
        border: 1px solid #d8bbf6;
        border-radius: 8px;
        background-color: #fefbff;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
        margin-bottom: 0.8rem;
    }
    .btn-submit {
        display: inline-block;
        background: linear-gradient(90deg, #7b2ff7, #f107a3);
        color: #fff;
        padding: 0.8rem 1.5rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    </style>
    <div class='form-card'>
        <h3>Please check your email for the verification link.</h3>
        <form action='resend_verification.php' method='POST' class='form'>
            <input type='email' name='email' placeholder='Enter your email to resend link' required>
            <button type='submit' class='btn-submit'>Resend Verification Email</button>
        </form>
    </div>
    HTML;
}
?>