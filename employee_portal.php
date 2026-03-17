<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, employee_name, username, password FROM employees WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        if (password_verify($password, $employee['password'])) {
            $_SESSION['employee_id'] = $employee['id'];
            $_SESSION['employee_name'] = $employee['employee_name'];
            header("Location: employee_portal.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
}

// Check if employee is logged in
if (!isset($_SESSION['employee_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Login - Sari-sari</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div class="container">
            <h1>Employee Login</h1>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Fetch orders for the logged-in employee
$employee_name = $_SESSION['employee_name'];
$orders_sql = "SELECT order_id, customer_name, contact_number, address, SUM(total_amount) as total_amount 
               FROM orders 
               WHERE employee_name = '$employee_name' 
               GROUP BY order_id, customer_name, contact_number, address";
$orders_result = $conn->query($orders_sql);

$orders_data = [];
while ($order = $orders_result->fetch_assoc()) {
    $order_id = $order['order_id'];
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
    <title>Employee Portal - Sari-sari</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Employee Portal - <?php echo htmlspecialchars($_SESSION['employee_name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <div class="button-container">
            <a href="employee_management_portal.php" class="employee-management-btn">Employee Management Portal</a>
            <a href="logout_employee.php" class="logout-btn">Logout</a>
        </div>

        <?php if (!empty($orders_data)): ?>
            <?php foreach ($orders_data as $order): ?>
                <div class="order-details">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($order['contact_number'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <p><strong>The Order:</strong></p>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p><strong>Total Amount:</strong> P<?php echo htmlspecialchars($order['total_amount'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No orders found for this employee.</p>
        <?php endif; ?>
    </div>
</body>
</html>