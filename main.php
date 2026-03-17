<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle employee registration
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_employee'])) {
    $employee_name = $conn->real_escape_string($_POST['employee_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if username already exists
    $check_sql = "SELECT username FROM employees WHERE username = '$username'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        $message = "Error: Username '$username' is already taken. Please choose a different username.";
    } else {
        $sql = "INSERT INTO employees (employee_name, username, password) VALUES ('$employee_name', '$username', '$password')";
        if ($conn->query($sql)) {
            $message = "Employee registered successfully!";
        } else {
            $message = "Error registering employee: " . $conn->error;
        }
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_sql = $search ? "WHERE item_name LIKE '%$search%'" : '';

$sql = "SELECT id, item_name, quantity, price FROM inventory $search_sql";
$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error);
}

$sales_sql = "SELECT s.sale_date, i.item_name, s.quantity_sold, i.price, (s.quantity_sold * i.price) AS sale_total
              FROM sales s
              JOIN inventory i ON s.item_id = i.id
              ORDER BY s.sale_date DESC";
$sales_result = $conn->query($sales_sql);

$total_sales_sql = "SELECT SUM(quantity_sold * price) AS total_sales FROM sales s
                    JOIN inventory i ON s.item_id = i.id";
$total_sales_result = $conn->query($total_sales_sql);
$total_sales = 0;
if ($total_sales_result->num_rows > 0) {
    $total_sales_row = $total_sales_result->fetch_assoc();
    $total_sales = $total_sales_row['total_sales'];
}

// Fetch employees
$employees_sql = "SELECT id, employee_name, created_at FROM employees ORDER BY created_at DESC";
$employees_result = $conn->query($employees_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function toggleSalesRecords() {
            var salesTable = document.getElementById("sales-records");
            if (salesTable.style.display === "none") {
                salesTable.style.display = "block";
            } else {
                salesTable.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Store Inventory</h1>
        <!-- Show Logout button only if user is logged in -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php endif; ?>

        <div class="form-container">
            <h2>Add New Stock</h2>
            <form action="insert_stock.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32)); ?>">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" required>
                
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required>
                
                <label for="price">Price</label>
                <input type="text" id="price" name="price" required>
                
                <button type="submit">Add Stock</button>
            </form>
        </div>

        <div class="search-container">
            <h2>Search Inventory</h2>
            <form action="main.php" method="GET">
                <label for="search">Search by Item Name</label>
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <h3>Total Sales: P<?php echo number_format($total_sales, 2); ?></h3>

        <div class="button-container">
            <a href="record_sales.php" class="record-sale-btn">Record Sale</a>
            <a href="view_orders.php" class="view-orders-btn">View Orders</a>
        </div>

        <div class="inventory-container">
            <h2>Current Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>P" . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>
                                        <a href='edit_stock.php?id=" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'>Edit</a>
                                        <a href='delete_stock.php?id=" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "' onclick=\"return confirm('Are you sure you want to delete this item?');\">Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No items found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <button onclick="toggleSalesRecords()">Show Sales Records</button>

        <div id="sales-records" style="display:none;">
            <h2>Sales Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sale Date</th>
                        <th>Item Name</th>
                        <th>Quantity Sold</th>
                        <th>Sale Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($sales_result->num_rows > 0) {
                        while($sale = $sales_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($sale['sale_date'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($sale['item_name'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($sale['quantity_sold'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>P" . htmlspecialchars($sale['sale_total'], ENT_QUOTES, 'UTF-8') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No sales records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Employee Name Register Section -->
        <div class="employee-register-container">
            <h2>Employee Name Register</h2>
            <?php if ($message) echo "<p style='color:" . (strpos($message, 'Error') === false ? 'green' : 'red') . ";'>$message</p>"; ?>
            <form action="main.php" method="POST">
                <label for="employee_name">Employee Name:</label>
                <input type="text" id="employee_name" name="employee_name" required>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="register_employee">Register Employee</button>
            </form>

            <h3>Registered Employees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Registered At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($employees_result->num_rows > 0) {
                        while ($employee = $employees_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($employee['employee_name'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($employee['created_at'], ENT_QUOTES, 'UTF-8') . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No employees registered</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>