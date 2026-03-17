<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }

    $conn = new mysqli('localhost', 'root', '', 'inventory_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $item_name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

    if (!$item_name || !$quantity || !$price) {
        die("Invalid input data.");
    }

    $stmt = $conn->prepare("INSERT INTO inventory (item_name, quantity, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $item_name, $quantity, $price);
    $stmt->execute();

    header("Location: main.php");
    exit();
}
?>