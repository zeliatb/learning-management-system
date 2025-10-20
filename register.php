<?php
// Connect to the database
require_once 'config/db_config.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $token = random_int(100000, 999999);

// Insert data into the students table
    $sql = "INSERT INTO students (firstname, lastname, email, password, gender, dob, token, email_verified)
            VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
            
    $stmt = $conn->prepare($sql); // Create prepared statement
    $stmt->bind_param("ssssssi", $firstname, $lastname, $email, $password, $gender, $dob, $token); // Bind values

    // Honeypot field protection
    if (!empty($_POST['phonenumber'])) {
        http_response_code(400);
        exit("Invalid submission detected.");
    }

    // Send verification email
    $verify_link = "https://zeliatbraimah.eagletechafrica.com/verify_email.php?token=" .$token;
    $subject = "Verify Your Email - Nova LMS";
    $message = "Hi $firstname,\n\nHere is your verification code: $token\n\nPlease click the link below to verify your email:\n$verify_link\n\nThank you,\nNova LMS";
    $headers = "From: noreply@zeliatbraimah.eagletechafrica.com\r\n";
    mail($email, $subject, $message, $headers);

    // Execute and handle result
    if ($stmt->execute()) {
    // Redirect to login page if successful
        echo "<script>alert('Registration successful! Please check your email to verify your account and log in.'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error; // Display error message if registration fails
    }

    $stmt->close();
    $conn->close();

}
?>