<?php
include('config.php');
// Define the number of products to display per page
$productsPerPage = 120;

session_start();
$owner_id = $_SESSION['userid']; // Replace with the actual seller_id
// Determine the current page
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $productsPerPage;


// Query to fetch products with pagination
$sql = "SELECT * FROM product WHERE status = true Order by productid LIMIT $productsPerPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Fetch all products as an associative array
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;

try {
    //echo $owner_id;
    $sql = "SELECT storeid
        FROM Store
        WHERE Ownerid = :owner_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':owner_id', $owner_id);
    $stmt->execute();
   

    // Fetch all products as an associative array
    $storeid = $stmt->fetchColumn();
    //echo $storeid[0]['storeid'];
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
    <!--上面選單 -->
    <h1>TradeOrbit</h1>
    
    <nav>
        <ul class="flex-nav">
            <li class="active"><a href="admin_product_page.php">Home page</a></li>
            <li class="active"><a href="admin_page.php">My profile</a></li>
            <li class="active"><a href="admin_see_user.php">User and Market Information</a></li>
        </ul>
    </nav>
    
    <div class="product-container">
        <?php
        
        //echo $_SESSION['userid'];
        // Check if there are any products
        
        if ($products) {
            // Output each product
            foreach ($products as $product) {
                echo "<div class='product'>";
                echo "<h3>{$product['product_name']}</h3>";
                echo "<p>Price: $ {$product['price']}</p>";
                echo "<p>Stock quantity: {$product['item_count']}</p>";
                $sql = "SELECT store_name FROM store WHERE storeID = {$product['storeid']}::text";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                // Fetch all products as an associative array
                $storename = $stmt->fetchColumn();

                $sql = "SELECT avg(star)
                FROM comment
                where productid = :productid
                Group by productid";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':productid', $product['productid']);
                $stmt->execute();
                $avg_review = $stmt->fetchColumn();
                $avg_review = round($avg_review,2);
                echo "<p>Avg review: {$avg_review}</p>";
                //echo "<p>Store: {$storeid} </p>";
                echo "<p>Store: <a href = 'admin_view_store.php?storeid=${product['storeid']}'  .  >{$storename}</a></p>";
                // Add the form for adding to cart
                echo "<form method='post'>";
                echo "<input type='hidden' name='productid' value='{$product['productid']}'>";
                echo "<button type='submit'>Remove</button>";
                echo "</form>";
                echo "</div>";

            }
        } else {
            echo "No products available.";
        }
        ?>
    <div>

    <!-- Pagination Links -->
    <nav>
        <ul class="pagination">
            <?php
                // Calculate the total number of pages

                $sql = "SELECT Count(*) FROM product";
                $stmt = $pdo->prepare($sql);
                
                // Bind parameters and execute the statement
                //$stmt->bindParam(":email", $email);
                $stmt->execute();
            
                // Get the result set
                $totalProducts = $stmt->fetchColumn();
                $productsPerPage = 100;
                $totalPages = ceil($totalProducts / $productsPerPage);

                // Display pagination links
                if ($current_page > 1) {
                    echo "<li class='active'><a class='page-link' href='?page=" . ($current_page - 1) . "'>Previous</a> </li>";

                }
                echo "<li class='active'><span class='current-page'><a class='page-link' href='?page=$current_page'>$current_page</a> </span></li>";
                for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $totalPages); $i++) {
                    // echo "<a href='?page=$i'>$i</a> ";
                    if($i > $current_page)
                    {
                        echo "<li class='active'><a class='page-link' href='?page=$i'>$i</a> </li>";
                    }
                    
                }
                

                if ($current_page < $totalPages) {
                    // echo "<a href='?page=" . ($current_page + 1) . "'>Next</a>";
                    echo "<li class='active'><a class='page-link' href='?page=" . ($current_page + 1) . "'>Next</a> </li>";

                }
            ?>
        </ul>
    </nav>

</body>
</html>

