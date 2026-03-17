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

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        die("Invalid item ID.");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("Invalid CSRF token.");
        }

        $item_name = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

        if (!$item_name || !$quantity || !$price) {
            die("Invalid input data.");
        }

        $stmt = $conn->prepare("UPDATE inventory SET item_name=?, quantity=?, price=? WHERE id=?");
        $stmt->bind_param("sidi", $item_name, $quantity, $price, $id);
        $stmt->execute();

        header("Location: main.php");
        exit();
    } else {
        $stmt = $conn->prepare("SELECT item_name, quantity, price FROM inventory WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
        } else {
            die("Item not found.");
        }
    }

    $conn->close();
} else {
    die("Invalid request.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Stock</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Edit Stock</h1>
        <div class="form-container">
            <form action="edit_stock.php?id=<?php echo $id; ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" name="item_name" value="<?php echo htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8'); ?>" required>
                
                <label for="price">Price</label>
                <input type="text" id="price" name="price" value="<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>" required>
                
                <button type="submit">Update Stock</button>
            </form>
        </div>
    </div>
</body>
</html>