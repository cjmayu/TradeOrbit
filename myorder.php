<?php
// Database connection
// include('config.php');
// session_start();
// $user_id = $_SESSION['userid'];

// // Fetch the order IDs placed by the user
// $query = "SELECT DISTINCT o.orderid
//           FROM orders o
//           WHERE o.buyerid = :user_id
//           ORDER by o.orderid";

// try {
//     $stmt = $pdo->prepare($query);
//     $stmt->bindParam(':user_id', $user_id);
//     $stmt->execute();
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// } catch (PDOException $e) {
//     die('Query failed: ' . $e->getMessage());
// }


?>


<?php
    // Database connection
    include('config.php');
    session_start();
    $user_id = $_SESSION['userid'];

    $query = "SELECT DISTINCT o.orderid, o.status, o.total_price, o.order_time
    FROM orders o
    JOIN order_include AS oi ON o.orderid = oi.orderid
    JOIN product AS p ON oi.productid = p.productid
    WHERE o.buyerid = :user_id
    ORDER by o.order_time Desc";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }

    try{
        $sql = "SELECT s.storeid
                FROM users As u
                JOIN Store As s ON s.ownerid = u.userid
                WHERE s.ownerid = :userid";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userid', $user_id);
        $stmt->execute();

        // Fetch all products as an associative array
        $storeid = $stmt->fetchColumn();

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My past orders</title>
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
<h1>My past orders</h1>
    
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
<?php
// Display the order IDs
//echo '<h2>Order IDs Placed by User ID ' . $user_id . '</h2>';
echo '<ul>';
echo '<table border="1">';
echo '<tr><th>Order ID</th><th>Order Time</th><th>Status</th><th>Price</th></tr>';
foreach ($result as $row) {
   
    do{
        
        echo '<tr>';
        echo '<td><a href="order_detail.php?orderid=' . $row['orderid'] . '">' . $row['orderid'] . '</a></td>';
        echo '<td>' . $row['order_time'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . $row['total_price'] . '</td>';

    } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
    
}

echo '</ul>';

// Display orders and products

// foreach ($result as $row) {
//     echo '<p><strong>Order ID:</strong> ' . $row['orderid'] . '</p>';
    
//     echo '<table border="1">';
//     echo '<tr><th>Product Name</th><th>Quantity</th><th>Price</th></tr>';
    
//     // Display products for each order
//     do {
//         echo '<tr>';
//         echo '<td>' . $row['product_name'] . '</td>';
//         echo '<td>' . $row['product_count'] . '</td>';
//         echo '<td>' . $row['total_price'] . '</td>';
//         echo '</tr>';
//     } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
    
//     echo '</table>';
// }
?>

</body>
</html>


