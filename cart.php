<?php
session_start(); // Start the session
$userid = $_SESSION['userid']; // Replace with the actual seller_id
if (!isset($_SESSION['userid'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Include the database configuration
include('config.php');

try{
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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



$emptycart = 'emptycart.jpg';
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
        .profile-picture {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background-image: url('<?php echo $emptycart; ?>');
        background-size: cover;
        margin-bottom: 20px;
        overflow: hidden;
        display: inline-block;

    }
    .profile-container {
        text-align: center;
    }
    </style>
</head>
<body>
    <!--上面選單 -->
    <h1>Your Cart</h1>
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

<?php
try {
    // Set the PDO error mode to exception
   
    // Retrieve the logged-in user's cart information
    $userid = $_SESSION['userid'];
    //echo "user id : $userid";

    // SQL query to select products in the user's cart
    $sql = "SELECT c.productid, c.product_count, p.product_name, p.item_count, c.status
            FROM add_to_cart c
            INNER JOIN product p ON c.productid = p.productid
            WHERE c.userid = :userid and c.status = true";

    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the statement
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result as an associative array
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are any products in the cart
    if ($cartItems) {
        echo '<table border="1">';
        echo '<tr><th>Product ID</th><th>Product Name</th><th>Product Count</th><th>Order</th></tr>';

        // Loop through the cart items and print the data
        foreach ($cartItems as $cartItem) {
            echo '<tr>';
            echo '<td>' . $cartItem['productid'] . '</td>';
            echo '<td>' . $cartItem['product_name'] . '</td>';
            echo '<td><input type="number" name="product_count[]" value="' . $cartItem['product_count'] . '" min="1" max="' . $cartItem['item_count'] . '"></td>';

           // echo '<td>' . ($cartItem['status'] ? $cartItem['status'] : 'false') . '</td>';
            echo '<td><input type="checkbox" class="click-checkbox" name="selected_products[]" value="' . $cartItem['productid'] . '"></td>';
            echo '</tr>';
        }

        echo '</table>';

        // Add the "Order" button
        echo '<form method="post" id="orderForm" action="order_processing.php">';
        echo '<button type="submit" id="orderButton">Order</button>';
        echo '</form>';

    } else {
        echo '<div class="profile-container">';
        echo '<h2>Your cart is empty.</h2>';
        echo '<div class="profile-picture"></div>';
        echo '</div>';
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Error: " . $e->getMessage();
} finally {
    // Close the database connection
    $pdo = null;
}
?>

</body>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var checkboxes = document.querySelectorAll('.click-checkbox');
    var selectedProducts = [];

    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            var productId = checkbox.value;
            var productCountInput = checkbox.closest('tr').querySelector('input[name="product_count[]"]');
            var productCount = productCountInput.value;

            // Toggle selection (add/remove product ID from the array)
            var index = selectedProducts.indexOf(productId);
            if (index === -1 && checkbox.checked) {
                selectedProducts.push({ productId, productCount });
            } else if (index !== -1 && !checkbox.checked) {
                selectedProducts.splice(index, 1);
            }

            // Log the current selected products
            console.log('Selected Products: ' + JSON.stringify(selectedProducts));
        });
    });

    // Add logic for the "Order" button click
    document.getElementById('orderButton').addEventListener('click', function () {
        // Add an input field to the form to send the selected product IDs
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_products';
        input.value = JSON.stringify(selectedProducts);

        // Append the input field to the form
        var form = document.getElementById('orderForm');
        form.appendChild(input);

        // Submit the form
        form.submit();
    });
});

</script>

</html>