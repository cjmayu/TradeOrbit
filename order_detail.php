<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TradeOrbit Home Page</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        /* Add your CSS styles here */
        

        .pagination {
            margin-top: 20px;
        }

        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h1>TradeOrbit</h1>
    
    <nav>
        <ul class="flex-nav">
            <li class="active"><a href="product_page.php">Home page</a></li>
            <li class="active"><a href="user_page.php">My profile</a></li>
            <li class="active"><a href="cart.php">My shopping cart</a></li>
            <li class="active"><a href="myorder.php">My past orders</a></li>
            <li class="dropdown-submenu"><a data-toggle="dropdown" >My store </a>
                <ul class="dropdown-menu">
                <li class="active"><a href="seller_market.php?storeid=<?php echo $storeid; ?>">My store page</a></li>
                <li><a href="prepare.php">My store orders</a></li>
                <ul class="dropdown-menu">
            <li><div class="dropdown">
        </ul>
    </nav>
    <h2>User Orders</h2>

<?php
// Database connection
include('config.php');
session_start();
$user_id = $_SESSION['userid'];
$order_id = isset($_GET['orderid']) ? urldecode($_GET['orderid']) : null;


// Fetch the products included in the order
$query = "SELECT p.product_name, oi.product_count, p.price
          FROM order_include oi
          JOIN product p ON oi.productid = p.productid
          WHERE oi.orderid = :order_id";

try {
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}

// Display the products included in the order
echo '<h2>Products Included in Order ID ' . $order_id . '</h2>';
echo '<table border="1">';
echo '<tr><th>Product Name</th><th>Quantity</th><th>Unit price</th></tr>';

foreach ($result as $row) {
    echo '<tr>';
    echo '<td>' . $row['product_name'] . '</td>';
    echo '<td>' . $row['product_count'] . '</td>';
    echo '<td>' . $row['price'] . '</td>';
    echo '</tr>';
}

echo '</table>';
?>

</body>
</html>
