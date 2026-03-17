<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['quantities'] ?? [];
    $items = $_POST['items'] ?? []; // These should be actual item names (e.g., "gin", "buwad")
    $totals = $_POST['totals'] ?? [];
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $employee_name = $conn->real_escape_string($_POST['employee_name']);

    // Generate a unique order_id
    $order_id = time() . rand(1000, 9999);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert individual items with the same order_id
        foreach ($quantities as $index => $quantity) {
            $quantity = intval($quantity);
            $total_amount = floatval($totals[$index]);
            $item_name = $conn->real_escape_string($items[$index]); // Ensure this is the actual item name

            if ($quantity > 0) {
                $item_sql = "INSERT INTO orders (order_id, item_name, quantity, total_amount, customer_name, contact_number, address, employee_name) 
                             VALUES ('$order_id', '$item_name', '$quantity', '$total_amount', '$customer_name', '$contact_number', '$address', '$employee_name')";
                if (!$conn->query($item_sql)) {
                    throw new Exception("Error inserting item: " . $conn->error);
                }
            }
        }
        $conn->commit();
        echo "Order placed successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}

$conn->close();
header("Location: home.php");
exit();
?>