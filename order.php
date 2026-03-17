<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch inventory items
$inventory_sql = "SELECT item_name, quantity, price FROM inventory";
$inventory_result = $conn->query($inventory_sql);

$inventory_items = [];
while ($row = $inventory_result->fetch_assoc()) {
    $inventory_items[] = $row;
}

// Fetch registered employees
$employees_sql = "SELECT employee_name FROM employees";
$employees_result = $conn->query($employees_sql);

$employees = [];
while ($employee = $employees_result->fetch_assoc()) {
    $employees[] = $employee['employee_name'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order - Sari-sari</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function calculateTotal(index) {
            const quantity = document.getElementsByName('quantities[]')[index].value;
            const price = document.getElementsByName('prices[]')[index].value;
            const total = document.getElementsByName('totals[]')[index];
            total.value = (quantity * price).toFixed(2);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Place Order</h1>
        <a href="home.php" class="back-btn">Back to Home</a>

        <form action="process_order.php" method="POST">
            <div class="customer-details">
                <h2>Customer Details</h2>
                <label for="customer_name">Customer Name:</label>
                <input type="text" id="customer_name" name="customer_name" required>

                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" required>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>

                <label for="employee_name">Employee Name:</label>
                <select id="employee_name" name="employee_name" required>
                    <option value="">Select an Employee</option>
                    <?php foreach ($employees as $employee): ?>
                        <option value="<?php echo htmlspecialchars($employee, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($employee, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="order-items">
                <h2>Order Items</h2>
                <?php foreach ($inventory_items as $index => $item): ?>
                    <div class="item">
                        <label><?php echo htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8'); ?> (Price: P<?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?>)</label>
                        <input type="hidden" name="items[]" value="<?php echo htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="prices[]" value="<?php echo htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="number" name="quantities[]" min="0" value="0" oninput="calculateTotal(<?php echo $index; ?>)">
                        <input type="text" name="totals[]" readonly value="0.00">
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>