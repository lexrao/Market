<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Sales Records</h1>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity Sold</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
               
                $conn = new mysqli('localhost', 'root', '', 'inventory_db');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

            
                $sql = "SELECT sales.quantity_sold, sales.sale_date, inventory.item_name
                        FROM sales
                        JOIN inventory ON sales.item_id = inventory.id";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['item_name']}</td>
                                <td>{$row['quantity_sold']}</td>
                                <td>{$row['sale_date']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No sales records found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
