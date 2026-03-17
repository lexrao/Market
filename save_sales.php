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

    $item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $quantity_sold = filter_input(INPUT_POST, 'quantity_sold', FILTER_VALIDATE_INT);
    $sale_date = date('Y-m-d');

    if (!$item_id || !$quantity_sold) {
        die("Invalid input data.");
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("SELECT quantity FROM inventory WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_stock = $row['quantity'];

            if ($current_stock >= $quantity_sold) {
                $stmt = $conn->prepare("INSERT INTO sales (item_id, quantity_sold, sale_date) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $item_id, $quantity_sold, $sale_date);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE inventory SET quantity = quantity - ? WHERE id = ?");
                $stmt->bind_param("ii", $quantity_sold, $item_id);
                $stmt->execute();

                $conn->commit();
                header("Location: main.php");
                exit();
            } else {
                die("Not enough stock available for the sale.");
            }
        } else {
            die("Item not found.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}
?>