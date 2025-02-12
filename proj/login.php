<?php
session_start();
require 'db.php';

$error = ""; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Check if the users table exists before running the query
        $checkTable = $conn->query("SHOW TABLES LIKE 'users'");
        if ($checkTable->num_rows == 0) {
            throw new Exception("Error: User database is missing. Please contact support.");
        }

        // Prepare and execute query
        if ($stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?")) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            if ($id) { // Check if user exists
                if (password_verify($password, $hashed_password)) {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['error'] = "The username or password you entered is incorrect.";
                }
            } else {
                $_SESSION['error'] = "The username or password you entered is incorrect.";
            }

            $stmt->close();
        } else {
            throw new Exception("Database query failed.");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirect to clear POST data and remove form resubmission issues
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>

        <!-- Display error message -->
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>{$_SESSION['error']}</p>";
            unset($_SESSION['error']); // Clear error message after displaying
        }
        ?>

        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <!-- Add Sign-Up link here -->
        <p class="change">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>