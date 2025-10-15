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

// Insert data into the students table
    $sql = "INSERT INTO students (firstname, lastname, email, password, gender, dob)
            VALUES (?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql); // Create prepared statement
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $password, $gender, $dob); // Bind values

// Execute and handle result
    if ($stmt->execute()) {
    // Redirect to login page if successful
        echo "<script>alert('Registration successful! Please log in.'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error; // Display error message if registration fails
    }

    $stmt->close();
    $conn->close();

}
?>