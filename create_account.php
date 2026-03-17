<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'inventory_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        die("All fields are required.");
    }

    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);
    if ($stmt->execute()) {
        echo "Account created successfully! <a href='login.php'>Login here</a>.";
    } else {
        if ($conn->errno === 1062) { // Duplicate username
            die("Username already exists.");
        } else {
            die("Error creating account: " . $conn->error);
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <form action="create_account.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Create Account</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>