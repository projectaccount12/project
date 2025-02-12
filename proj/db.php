<?php
$host = "localhost";
$user = "newuser";
$pass = "newpassword";      // Change if your MySQL has a password
$db = "user_auth";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>