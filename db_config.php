<?php

// Connect to the database
$servername = "localhost";
$username = "u376937047_zeliatb_admin";
$password = "SWPdacc0!J";
$dbname = "u376937047_lms_zeliatDB";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>