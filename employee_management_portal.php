<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'inventory_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if employee is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: employee_portal.php");
    exit();
}

// Handle employee registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_employee'])) {
    $employee_name = $conn->real_escape_string($_POST['employee_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO employees (employee_name, username, password) VALUES ('$employee_name', '$username', '$password')";
    if ($conn->query($sql)) {
        $message = "Employee registered successfully!";
    } else {
        $error = "Error registering employee: " . $conn->error;
    }
}

// Handle employee update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_employee'])) {
    $employee_id = $conn->real_escape_string($_POST['employee_id']);
    $employee_name = $conn->real_escape_string($_POST['employee_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $update_sql = "UPDATE employees SET employee_name = '$employee_name', username = '$username'";
    if ($password) {
        $update_sql .= ", password = '$password'";
    }
    $update_sql .= " WHERE id = '$employee_id'";

    if ($conn->query($update_sql)) {
        $message = "Employee updated successfully!";
    } else {
        $error = "Error updating employee: " . $conn->error;
    }
}

// Handle employee deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $delete_sql = "DELETE FROM employees WHERE id = '$delete_id'";
    if ($conn->query($delete_sql)) {
        $message = "Employee deleted successfully!";
    } else {
        $error = "Error deleting employee: " . $conn->error;
    }
}

// Fetch all employees
$employees_sql = "SELECT id, employee_name, username, created_at FROM employees ORDER BY created_at DESC";
$employees_result = $conn->query($employees_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management Portal - Sari-sari</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Employee Management Portal</h1>
        <a href="employee_portal.php" class="back-btn">Back to Employee Portal</a>
        <a href="logout_employee.php" class="logout-btn">Logout</a>

        <?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <!-- Register New Employee -->
        <div class="employee-register-container">
            <h2>Register New Employee</h2>
            <form action="employee_management_portal.php" method="POST">
                <label for="employee_name">Employee Name:</label>
                <input type="text" id="employee_name" name="employee_name" required>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="register_employee">Register Employee</button>
            </form>
        </div>

        <!-- Employee List -->
        <div class="employee-list-container">
            <h2>Employee List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Username</th>
                        <th>Registered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($employees_result->num_rows > 0) {
                        while ($employee = $employees_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . htmlspecialchars($employee['employee_name'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($employee['username'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>" . htmlspecialchars($employee['created_at'], ENT_QUOTES, 'UTF-8') . "</td>
                                    <td>
                                        <a href='employee_management_portal.php?edit_id=" . htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') . "'>Edit</a>
                                        <a href='employee_management_portal.php?delete_id=" . htmlspecialchars($employee['id'], ENT_QUOTES, 'UTF-8') . "' onclick=\"return confirm('Are you sure you want to delete this employee?');\">Delete</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No employees found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Employee Form (shown when edit_id is set) -->
        <?php if (isset($_GET['edit_id'])): ?>
            <?php
            $edit_id = $conn->real_escape_string($_GET['edit_id']);
            $edit_sql = "SELECT id, employee_name, username FROM employees WHERE id = '$edit_id'";
            $edit_result = $conn->query($edit_sql);
            $edit_employee = $edit_result->fetch_assoc();
            ?>
            <div class="employee-edit-container">
                <h2>Edit Employee</h2>
                <form action="employee_management_portal.php" method="POST">
                    <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($edit_employee['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <label for="employee_name">Employee Name:</label>
                    <input type="text" id="employee_name" name="employee_name" value="<?php echo htmlspecialchars($edit_employee['employee_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_employee['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    <label for="password">New Password (leave blank to keep current):</label>
                    <input type="password" id="password" name="password">
                    <button type="submit" name="update_employee">Update Employee</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>