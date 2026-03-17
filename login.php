<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli('localhost', 'root', '', 'inventory_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        
    }

    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header("Location: main.php");
            exit();
        } else {
            die("Invalid username or password.");
        }
    } else {
        die("Invalid username or password.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <p>Don't have an account? <a href="create_account.php">Create one here</a>.</p>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>