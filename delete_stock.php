<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $conn = new mysqli('localhost', 'root', '', 'inventory_db');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$item_id) {
        die("Invalid item ID.");
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM sales WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        $conn->commit();
        header("Location: main.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error deleting item and sales records: " . $e->getMessage());
    }
} else {
    echo "Invalid request.";
    exit();
}
?>