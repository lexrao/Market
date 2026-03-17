<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle order confirmation
if (isset($_GET['confirm_order_id'])) {
    $order_id = $conn->real_escape_string($_GET['confirm_order_id']);
    $update_sql = "UPDATE orders SET status = 'confirmed' WHERE order_id = '$order_id'";
    if ($conn->query($update_sql)) {
        $message = "Order confirmed successfully!";
    } else {
        $error = "Error confirming order: " . $conn->error;
    }
}

// Fetch orders data grouped by order_id
$orders_sql = "SELECT order_id, customer_name, contact_number, address, employee_name, SUM(total_amount) as total_amount, status 
               FROM orders 
               GROUP BY order_id, customer_name, contact_number, address, employee_name, status";
$orders_result = $conn->query($orders_sql);

$orders_data = [];
while ($order = $orders_result->fetch_assoc()) {
    $order_id = $order['order_id'];
    // Fetch items for this order
    $items_sql = "SELECT item_name, quantity FROM orders WHERE order_id = '$order_id'";
    $items_result = $conn->query($items_sql);
    $items_list = [];
    while ($item = $items_result->fetch_assoc()) {
        $items_list[] = htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8');
    }
    $order['items'] = $items_list;
    $orders_data[] = $order;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders - Sari-sari</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>View Orders</h1>
        <a href="main.php" class="back-btn">Back to Inventory</a>

        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <?php if (!empty($orders_data)): ?>
            <?php foreach ($orders_data as $order): ?>
                <div class="order-details">
                    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Customer Number:</strong> <?php echo htmlspecialchars($order['contact_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <p><strong>The Order:</strong></p>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p><strong>Total Amount:</strong> P<?php echo htmlspecialchars($order['total_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($order['employee_name'], ENT_QUOTES, 'UTF-8'); ?></p>

                    <p><strong>Status:</strong> <?php echo htmlspecialchars(ucfirst($order['status']), ENT_QUOTES, 'UTF-8'); ?></p>

                    <p>
                        <a href="remove_order.php?order_id=<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>" 
                           onclick="return confirm('Are you sure you want to remove this order?');">Remove</a>
                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="view_orders.php?confirm_order_id=<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>" 
                               onclick="return confirm('Are you sure you want to confirm this order?');">Confirm the Order</a>
                        <?php endif; ?>
                    </p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>