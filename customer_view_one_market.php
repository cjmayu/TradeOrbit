<?php
include('config.php');
session_start();
// Seller ID (you may get this from the login session or another source)
$userid = $_SESSION['userid']; // Replace with the actual seller_id

$thisstoreid = isset($_GET['storeid']) ? urldecode($_GET['storeid']) : null;
?>
<?php
try{
    $sql = "SELECT s.storeid
            FROM users As u
            JOIN Store As s ON s.ownerid = u.userid
            WHERE s.ownerid = :userid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    // Fetch all products as an associative array
    $storeid = $stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
try {
    

    // Query to fetch products for a specific seller
    $sql = "SELECT p.productid, s.store_name, p.Product_name, p.Price, p.Item_count
            FROM Product AS p
            JOIN Store As s ON p.storeid = s.storeid
            WHERE s.storeid = :thisstoreid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':thisstoreid', $thisstoreid);
    $stmt->execute();

    // Fetch all products as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "SELECT s.store_name
            FROM Product AS p
            JOIN Store As s ON p.storeid = s.storeid
            WHERE s.storeid = :thisstoreid";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':thisstoreid', $thisstoreid);
    $stmt->execute();

    // Fetch all products as an associative array
    $store_name = $stmt->fetchColumn();

    $sql = "SELECT avg(star)
            FROM comment
            where productid = :productid
            Group by productid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':productid', $product['productid']);
    $stmt->execute();
    $avg_review = $stmt->fetchColumn();
    $avg_review = round($avg_review,2);

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
<?php
// Assuming you have a database connection established
// Handle the follow button click
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["follow"])) {
    // Retrieve user ID and store ID from the form
    $userid = $_SESSION['userid']; // Assuming you already have this value
    $thisstoreid = isset($_POST['storeid']) ? $_POST['storeid'] : null;
    // Insert a new row into the FOLLOW table
    $sql = "SELECT count(*) From Follow where userid = :userid and storeid = :storeid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userid', $userid);
    $stmt->bindParam(':storeid', $thisstoreid);
    $stmt->execute();
    $cnt = $stmt->fetchColumn();
    if(!$cnt)
    {
        $sql = "INSERT INTO follow (userid, storeid) VALUES (:userid, :storeid)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':storeid', $thisstoreid);

        $stmt->execute();
    }

    header("Location: customer_view_one_market.php?storeid=" . urlencode($thisstoreid));

    // echo "New record inserted successfully";
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
    <h1>Store page</h2>
    <!--上面選單 -->
    
    
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
    <h2><?php echo $store_name?></h2>    <!-- Follow Button Form -->
    <div style="display: flex; justify-content: center; align-items: center;">
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <!-- Use the actual PHP variables for userid and storeid -->
        <input type="hidden" name="userid" value="<?php echo $userid; ?>">
        <input type="hidden" name="storeid" value="<?php echo $thisstoreid; ?>">
        <button type="submit" name="follow">Follow</button>
    </form>
</div>
    <!--確認是不是有market-->

    
        <div class = "product-container">
        <?php
            if ($products) {
                // Output each product
                foreach ($products as $product) {
                    echo "<div class='product'>";
                    echo "<h3>{$product['product_name']}</h3>";
                    echo "<p>Price: $ {$product['price']}</p>";
                    echo "<p>Stock quantity: {$product['item_count']}</p>";

    
                    
                    echo "<p>Avg review: {$avg_review}</p>";
                    //echo "<p>Store: {$storeid} </p>";
                    echo "<p>Store: <a href = 'customer_view_one_market.php?storeid=$thisstoreid'  .  >{$product['store_name']}</a></p>";
                    // Add the form for adding to cart
                    echo "<form method='post' action='addToCart.php'>";
                    echo "<input type='hidden' name='productid' value='{$product['productid']}'>";
                    echo "<button type='submit'>Add to Cart</button>";
                    echo "</form>";
                    echo "</div>";
    
                }
            } else {
                echo "No products available.";
            }
        ?>
        </div>

    
</body>
</html>
