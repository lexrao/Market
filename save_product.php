<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Sales</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Record Sales</h1>
        <form action="save_sales.php" method="POST">
            <label for="item_id">Item</label>
            <select id="item_id" name="item_id" required>
                <?php
                $conn = new mysqli('localhost', 'root', '', 'inventory_db');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
           
                $sql = "SELECT id, item_name FROM inventory";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['item_name']}</option>";
                }
                $conn->close();
                ?>
            </select>

            <label for="quantity_sold">Quantity Sold</label>
            <input type="number" id="quantity_sold" name="quantity_sold" min="1" required>

            <button type="submit">Record Sale</button>
        </form>
    </div>
</body>
</html>
