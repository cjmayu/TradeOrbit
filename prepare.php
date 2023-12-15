<?php
// Database connection
include('config.php');
session_start();
$owner_id = $_SESSION['userid']; // Replace with the actual seller_id
try{
    $sql = "SELECT s.storeid
            FROM users As u
            JOIN Store As s ON s.ownerid = u.userid
            WHERE s.ownerid = :userid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $owner_id);
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
    <title>List of Products</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        /* Add your CSS styles here */

        .add-product-form {
            margin-top: 20px;
        }

        .form-field {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
</html>

     <!--上面選單 -->
     <h1>Products to Prepare</h1>
     <nav>
        <ul class="flex-nav">
            <li class="active"><a href="product_page.php">Home page</a></li>
            <li class="active"><a href="user_page.php">My profile</a></li>
            <li class="active"><a href="cart.php">My shopping cart</a></li>
            <li class="active"><a href="myorder.php">My past orders</a></li>
            <li class="dropdown-submenu"><a href="#" data-toggle="dropdown" >My store </a>
                <ul class="dropdown-menu">
                <li class="active"><a href="seller_market.php?storeid=<?php echo $storeid; ?>">My store page</a></li>
                <li><a href="prepare.php">My store orders</a></li>
                <ul class="dropdown-menu">
            <li><div class="dropdown">
        </ul>
    </nav>
<body>



</body>
<?php




//$store_id = isset($_GET['storeid']) ? urldecode($_GET['storeid']) : null;
// echo $storeid;
//$store_id = $_SESSION['storeid'];

// Fetch products to prepare along with order details
$query = "SELECT prepare.orderid, product.status,  order_include.productid, order_include.product_count AS prepare_quantity, product.product_name
          FROM prepare
          JOIN order_include ON prepare.orderid = order_include.orderid
          JOIN product ON order_include.productid = product.productid
          WHERE prepare.storeid = :storeid";

try {
    $stmt = $pdo->prepare($query);  // Use prepare() instead of query()
    $stmt->bindParam(':storeid', $storeid);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}

// Display products to prepare along with order details
//echo '<h2>Products to Prepare</h2>';
echo '<table border="1">';
echo '<tr><th>Order ID</th><th>Product Name</th><th>Quantity to Prepare</th><th>Status</th><th>Action</th></tr>';

foreach ($result as $row) {
    echo '<tr>';
    echo '<td>' . $row['orderid'] . '</td>';
    echo '<td>' . $row['product_name'] . '</td>';
    echo '<td>' . $row['prepare_quantity'] . '</td>';
    echo '<td>' . $row['status'] . '</td>';
    echo '<td>';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="order_id" value="' . $row['orderid'] . '">';
    echo '<select name="status">';
    echo '<option value="備貨中">備貨中</option>';
    echo '<option value="已備貨">已備貨</option>';
    echo '</select>';
    echo '<button type="submit" name="edit_status">Edit</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';

// Check if the form is submitted for status update
if (isset($_POST['edit_status'])) {
    $order_id_to_update = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update the status in the prepare table
    $update_status_query = "UPDATE product SET status = :new_status WHERE orderid = :order_id and productid = :productid";
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare($update_status_query);
        $stmt->bindParam(':order_id', $order_id_to_update, PDO::PARAM_INT);
        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':productid', $result['productid'], PDO::PARAM_INT);
        $stmt->execute();
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die('Update failed: ' . $e->getMessage());
    }
}
?>
