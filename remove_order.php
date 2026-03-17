<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['order_id'])) {
    $order_id = $conn->real_escape_string($_GET['order_id']);
    $sql = "DELETE FROM orders WHERE order_id = '$order_id'";
    if ($conn->query($sql)) {
        echo "Order removed successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
header("Location: home.php");
exit();
?>