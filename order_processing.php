<?php
session_start();

// Include the database configuration
include('config.php');

// Get the product data from the form
$productDataJson = isset($_POST['selected_products']) ? $_POST['selected_products'] : '[]';
// Decode the JSON string into a PHP array
$productData = json_decode($productDataJson, true);

// Check if $productIds is an array
if (!is_array($productData)) {
    echo 'Invalid product data format.';
    exit();
}

// Retrieve product details based on the selected product IDs
$productDetails = [];
$totalPrice = 0; // Initialize total price

foreach ($productData as $productItem) {
    $productId = $productItem['productId'];
    $productCount = $productItem['productCount'];
    $sql = "SELECT productid, product_name, price FROM product WHERE productid = :productid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':productid', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Add product count to the result array
    $product['product_count'] = $productCount;
    $productDetails[] = $product;

    // Calculate individual product price and add to the total
    $totalPrice += $product['price'] * $productCount;

}

// Display the order summary page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <!-- Add your CSS styles here -->
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h2>Order Summary</h2>

    <table border="1">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Count</th>
            <th>Product Price</th>
        </tr>
        <?php foreach ($productDetails as $product): ?>
            <tr>
                <td><?php echo $product['productid']; ?></td>
                <td><?php echo $product['product_name']; ?></td>
                <td><?php echo $product['product_count']; ?></td>
                <td><?php echo $product['price'] * $product['product_count']; ?></td>

            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Add a row for total price -->
    <table border="1">
        <tr>
            <th>Total Price</th>
            <td colspan="3"><?php echo $totalPrice; ?></td>
        </tr>
    </table>


    <!-- Add form for selecting delivery and payment methods -->
    <form action="order_include.php" method="post">
        <label for="deliveryMethod">Select Delivery Method:</label>
        <select name="deliveryMethod" id="deliveryMethod">
            <!-- Add options for delivery methods -->
            <option value="宅配">宅配</option>
            <option value="7-ELEVEN">7ELEVEN</option>
            <option value="全家">全家</option>
            <option value="萊爾富">萊爾富</option>
            <option value="OK">OK</option>
            <!-- Add more options as needed -->
        </select>

        <br>

        <label for="paymentMethod">Select Payment Method:</label>
        <select name="paymentMethod" id="paymentMethod">
            <!-- Add options for payment methods -->
            <option value="信用卡">信用卡</option>
            <option value="LinePay">LinePay</option>
            <option value="貨到付款">貨到付款</option>
            <!-- Add more options as needed -->
        </select>

        <br>

        <!-- Add hidden input field for productDataJson -->
        <input type="hidden" name="total_price" value="<?php echo htmlspecialchars(json_encode($totalPrice)); ?>">
        <input type="hidden" name="productDataJson" value="<?php echo htmlspecialchars(json_encode($productData)); ?>">

        <button type="submit">Place Order</button>
    </form>
</body>
</html>