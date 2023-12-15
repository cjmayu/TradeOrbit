<?php
include('config.php');
session_start();
// Seller ID (you may get this from the login session or another source)
$owner_id = $_SESSION['userid']; // Replace with the actual seller_id
$storeid = isset($_GET['storeid']) ? urldecode($_GET['storeid']) : null;
//echo $storeid;
// Handle the form submission for adding a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if(!empty($storeid))
        {
            // SQL query to select the maximum value of a column
            $get_productID_sql = "SELECT MAX(CAST(productid AS INTEGER)) AS max_value FROM Product";

            // Execute the query
            $result = $pdo->query($get_productID_sql);

            // Fetch the result as an associative array
            $row = $result->fetch(PDO::FETCH_ASSOC);

            // Get the maximum value
            $maxValue = $row['max_value'] + 1;
            // Output the result or use it as needed
            //echo "The maximum value is: $maxValue";


            // Get the form data
            $productName = $_POST['product_name'];
            $productPrice = $_POST['price'];
            $productItemCnt = $_POST['item_count'];
            $sql = "SELECT storeid
                FROM Store
                WHERE Ownerid = :owner_id";

            


            // Insert the new product into the database
            $query = "INSERT INTO product (productid, product_name, price, storeid, item_count) 
                    VALUES (:maxValue, :product_name, :price, :storeid, :item_count)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':maxValue', $maxValue);
            $stmt->bindParam(':product_name', $productName);
            $stmt->bindParam(':price', $productPrice);
            
            $stmt->bindParam(':storeid', $storeid);

            $stmt->bindParam(':item_count', $productItemCnt);
            $stmt->execute();

            // Redirect to prevent form resubmission on page refresh
            header("Location: " . $_SERVER['PHP_SELF'] . '?storeid=' . urlencode($storeid));
        }
        else {
            // Get the form data
            $store_name = isset($_POST['store_name']) ? $_POST['store_name'] : null;
    
            if (!empty($store_name)) {
                // SQL query to select the maximum value of a column
                $get_store_sql = "SELECT MAX(CAST(storeid AS INTEGER)) AS max_value FROM Store";
    
                // Execute the query
                $result = $pdo->query($get_store_sql);
    
                // Fetch the result as an associative array
                $row = $result->fetch(PDO::FETCH_ASSOC);
    
                // Get the maximum value
                $maxValue = $row['max_value'] + 1;
    
                // Insert the new product into the database
                $query = "INSERT INTO store (storeid, store_name, ownerid) 
                        VALUES (:maxValue, :store_name, :owner_id)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':maxValue', $maxValue);
                $stmt->bindParam(':store_name', $store_name);
                $stmt->bindParam(':owner_id', $owner_id);
                $stmt->execute();
    
                // Redirect to prevent form resubmission on page refresh
                header("Location: " . $_SERVER['PHP_SELF'] . '?storeid=' . urlencode($maxValue));
                exit();
            } else {
                // Handle the case when store_name is not provided
                echo "Store name is required.";
            }
        }
        exit();
}

try {
    // Query to fetch products for a specific seller
    $sql = "SELECT p.productid, p.product_name, p.price, p.item_count, p.status
            FROM Product AS p
            JOIN Store As s ON p.storeid = s.storeid
            WHERE s.Ownerid = :owner_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':owner_id', $owner_id);
    $stmt->execute();

    // Fetch all products as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
} finally {
    // Close the database connection
    $pdo = null;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Products</title>
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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

     <!--上面選單 -->
     <h1>My market</h1>
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
                </ul>
            <li><div class="dropdown">
        </ul>
    </nav>
    
    <!--確認是不是有market-->

    <?php if ($storeid): ?>
        
        <div class="container">
            <h3>Add a New Product</h3>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?storeid=' . urlencode($storeid); ?>">
                <div>
                    <label for="product_name">Product Name:</label>
                    <input type="text" class = "underLineStyle inputPaddingLeft" name="product_name" required>
                </div>
                <div>
                    <label for="price">Price:</label>
                    <input type="text" class = "underLineStyle inputPaddingLeft" name="price" required>
                </div>
                <div>
                    <label for="item_count">Stock:</label>
                    <input class = "underLineStyle inputPaddingLeft" name="item_count" required>
                </div>
                <div>
                    <button type="submit">Add Product</button>
                </div>
            </form>
        </div>
        <div class = "product-container">
        <?php
            if (count($products) > 0) {
                // Output each product
                foreach ($products as $product) {
                    if($product['status'])
                    {
                        echo "<div class='product'>";
                        echo "<h3>{$product['product_name']}</h3>";
                        echo "<p>Price: $ {$product['price']}</p>";
                        echo "<p>Stock quantity: {$product['item_count']}</p>";

                        echo "<form method='get' action='edit_product.php'>";
                        echo "<input type='hidden' name='productid' value='{$product['productid']}'>";
                        echo "<button type='submit'>Edit</button>";
                        echo "</form>";

                        echo "</div>";
                    }
                }
                
            } else {
                echo "No products available for this seller.";
            }
        ?>
        </div>
        <?php else: ?>
            <div class="container">
                <h3>Create my store</h3>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div>
                        <label for="store_name">Store Name:</label>
                        <input type="text" class = "underLineStyle inputPaddingLeft" name="store_name" required>
                    </div>
                    <div>
                        <button type="submit">Create store</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <!-- Pagination Links -->


    
<body>
<script>
$(function(){
    $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
        // 點擊時避免跟隨href位置
        event.preventDefault();
        // 避免在點擊時關閉菜單
        event.stopPropagation();
        if($(this).parent().hasClass('open') == false){ // 當 class=open 為否時
            $(this).parent().addClass('open');
        } else {
            $(this).parent().removeClass('open');
        }
    });
});
</script>