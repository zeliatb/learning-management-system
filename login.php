<?php
session_start();
require_once 'config/db_config.php';  // Database connection

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data
    $email = $_POST['email'];
    $password = $_POST['password'];

// Prepare SQL to fetch student by email
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Create session and redirect to dashboard
            $_SESSION['student_id'] = $row['sid'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['loggedin'] = true;
            header("Location: dashboard.php");
            exit;
        }   else {
            echo "<script>alert('Invalid password'); window.location='login.html';</script>";
        }
    }   else {
            echo "<script>alert('Email not found'); window.location='login.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>