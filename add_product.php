<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Add New Product</h1>
        <form action="save_product.php" method="POST">
            <label for="item_name">Item Name</label>
            <input type="text" id="item_name" name="item_name" required>
            
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
            
            <label for="price">Price</label>
            <input type="text" id="price" name="price" required>
            
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
