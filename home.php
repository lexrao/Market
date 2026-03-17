<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch inventory data
$sql = "SELECT item_name, quantity, price FROM inventory";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->connect_error);
}

$inventory_data = [];
while ($row = $result->fetch_assoc()) {
    $inventory_data[] = $row;
}

// Fetch orders data grouped by order_id
$orders_sql = "SELECT order_id, customer_name, contact_number, address, employee_name, SUM(total_amount) as total_amount 
               FROM orders 
               GROUP BY order_id, customer_name, contact_number, address, employee_name";
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
    $order['items'] = implode(", ", $items_list);
    $orders_data[] = $order;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sari-sari</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Sari-sari</h1>

        <!-- Login Button for Admin -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="login-prompt">
                <a href="login.php" class="login-btn">Login as Admin</a>
            </div>
        <?php else: ?>
            <!-- Logout Button -->
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php endif; ?>

        <!-- Description -->
        <div class="description">
            <p>Welcome to our Sari-sari. Here, you can view the current stock levels, search for specific items, and stay updated on upcoming stocks or events.</p>
        </div>

        <!-- Highlights: Upcoming Stocks or Events -->
        <div class="highlights">
            <h2>Upcoming Stocks or Events</h2>
            <ul>
                <li>New shipment of <strong>Buwad</strong> arriving next week.</li>
                <li>Special discount on <strong>Gin</strong> for the holiday season.</li>
                <li>New product launch: <strong>Milo</strong> coming soon.</li>
            </ul>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form action="home.php" method="GET">
                <input type="text" name="search" placeholder="Search items..." value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Order and Employee Portal Buttons -->
        <div class="button-container">
            <a href="order.php" class="order-btn">Place Order</a>
            <a href="employee_portal.php" class="employee-portal-btn">Employee Portal</a>
        </div>

        <!-- Stock Levels Table -->
        <h2>Current Stock Levels</h2>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price (P)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($inventory_data)): ?>
                    <?php foreach ($inventory_data as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No items found in inventory.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Orders Table -->
        <h2>Current Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Total Amount (P)</th>
                    <th>Customer Name</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Employee Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders_data)): ?>
                    <?php foreach ($orders_data as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['items'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['total_amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['contact_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['employee_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a href="remove_order.php?order_id=<?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?>" 
                                   onclick="return confirm('Are you sure you want to remove this order?');">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Contacts Section -->
        <div class="contacts">
            <h2>Contact Us</h2>
            <ul>
                <li><strong>Email:</strong> sari-sari@example.com</li>
                <li><strong>Phone:</strong> +63 912 345 6789</li>
                <li><strong>Address:</strong> 123 Sari-sari Street, Barangay Maligaya, City of Joy</li>
            </ul>
        </div>
    </div>
</body>
</html>