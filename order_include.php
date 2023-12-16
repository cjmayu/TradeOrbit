<?php
// session_start(); // Start the session

// if (!isset($_SESSION['userid'])) {
//     // Redirect to the login page if not logged in
//     header("Location: login.php");
//     exit();
// }
// $userid = $_SESSION['userid']; // Replace with the actual seller_id
// // Include the database configuration
// include('config.php');

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     try {
//         // Set the PDO error mode to exception
//         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         // Get the order ID
//         $get_orderid_sql = "SELECT MAX(CAST(orderid AS INTEGER)) AS max_value FROM orders";
//         $result = $pdo->query($get_orderid_sql);
//         $row = $result->fetch(PDO::FETCH_ASSOC);
//         $orderid = $row['max_value'] + 1;

//         // // Retrieve the logged-in user's information
//         // $userid = $_SESSION['userid'];

//         // Get delivery method and payment method from the form
//         $deliveryMethod = isset($_POST['deliveryMethod']) ? $_POST['deliveryMethod'] : 'default_delivery_method';
//         $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : 'default_payment_method';
        
//         // Calculate the total price (assuming you have this value from the previous page)
//         $total_price = isset($_POST['total_price']) ? $_POST['total_price'] : 0;
        
//         // Set the default values for status, address, and order time
//         $status = '未出貨';
//         $address = 'taiwan, taipei';
//         $orderTime = date('Y-m-d H:i:s'); // Current date and time

//         // Begin a transaction
//         $pdo->beginTransaction();

//         // Insert data into the ORDER table
//         $sqlOrder = "INSERT INTO orders (orderid, buyerid, status, address, delivery_method, payment_method, order_time, total_price)
//                      VALUES (:orderid, :userid, :status, :address, :deliveryMethod, :paymentMethod, :orderTime, :total_price)";   
        
//         $stmtOrder = $pdo->prepare($sqlOrder);
//         $stmtOrder->bindParam(':orderid', $orderid, PDO::PARAM_INT);
//         $stmtOrder->bindParam(':userid', $userid, PDO::PARAM_INT);
//         $stmtOrder->bindParam(':status', $status);
//         $stmtOrder->bindParam(':address', $address);
//         $stmtOrder->bindParam(':deliveryMethod', $deliveryMethod);
//         $stmtOrder->bindParam(':paymentMethod', $paymentMethod);
//         $stmtOrder->bindParam(':orderTime', $orderTime);
//         $stmtOrder->bindParam(':total_price', $total_price);
//         $stmtOrder->execute();

        

//         // Insert data into ORDER_INCLUDE table
//         // Get the product data from the form
//         $productDataJson = isset($_POST['productDataJson']) ? $_POST['productDataJson'] : '[]';
//         $productData = json_decode(htmlspecialchars_decode($productDataJson), true);
        
//         // Begin a transaction
//         // $pdo->beginTransaction();

//         $sqlOrderInclude = "INSERT INTO ORDER_INCLUDE (orderid, productid, product_count)
//                             VALUES (:orderid, :productid, :product_count)";    
//         $stmtOrderInclude = $pdo->prepare($sqlOrderInclude);

//         // echo 'Count:',$count;
//         foreach ($productData as $product) {
//             $productid = $product['productId'];
//             $product_count = $product['productCount'];
    
//             // Assuming $orderId is the ID of the order you obtained earlier
//             $stmtOrderInclude->bindParam(':orderid', $orderid, PDO::PARAM_INT);
//             $stmtOrderInclude->bindParam(':productid', $productid, PDO::PARAM_INT);
//             $stmtOrderInclude->bindParam(':product_count', $product_count, PDO::PARAM_INT);
//             $stmtOrderInclude->execute();
//         }


//                 // Begin a transaction

//             $sql = "UPDATE Add_to_cart
//                     SET status = false, product_count = 0
//                     where userid = :userid and productid = :productid";    
//             $stmt= $pdo->prepare($sql);

//             // echo 'Count:',$count;
//             foreach ($productData as $product) {
//                 $productid = $product['productId'];
//                 $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
//                 $stmt->bindParam(':productid', $productid, PDO::PARAM_INT);
//                 $stmt->execute();
//             }
//                     // Commit the transaction
//         echo 'Order placed successfully!','<br>' ;
//         $pdo->commit();
//         // Commit the transaction
//         echo 'Order_include placed successfully!','<br>' ;
//         header("Location: myorder.php");

//     } catch (PDOException $e) {
//         // Handle database connection errors
//         $pdo->rollBack();
//         echo 'Update failed: ' . $e->getMessage();
//     } 
//     finally {
//         // Close the database connection
//         $pdo = null;
//     }
// } else {
//     // Redirect to the cart page if accessed without a POST request
//     header("Location: cartPage.php");
//     exit();
// }
?>














<?php
session_start(); // Start the session

if (!isset($_SESSION['userid'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}
$userid = $_SESSION['userid']; // Replace with the actual seller_id
// Include the database configuration
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $productDataJson = isset($_POST['productDataJson']) ? $_POST['productDataJson'] : '[]';
    $productData = json_decode(htmlspecialchars_decode($productDataJson), true);
    try {
        //begin transaction

        $pdo->beginTransaction();
        // lock 看庫存量
        foreach ($productData as $product) {
            $productid = $product['productId'];
            $product_count = $product['productCount'];
            $lock = "SELECT item_count FROM product WHERE productid = :productid FOR UPDATE";
            $stmt_lock = $pdo -> prepare($lock);
            $stmt_lock->bindParam(':productid', $productid, PDO::PARAM_INT);
            $stmt_lock -> execute();
            $stock = $stmt_lock->fetchColumn();
            if ($stock - $product_count > 0) {
                $updateSQL = "UPDATE product SET item_count = item_count - :product_count WHERE productid = :productid";
                $stmtUpdate = $pdo->prepare($updateSQL);
                $stmtUpdate->bindParam(':product_count', $product_count, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':productid', $productid, PDO::PARAM_INT);
                $stmtUpdate->execute(); // Fix the typo here
            }
        }

        echo "here ";
        //加上新orders
        // Get the order ID
        $get_orderid_sql = "SELECT MAX(CAST(orderid AS INTEGER)) AS max_value FROM orders";
        $result = $pdo->query($get_orderid_sql);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $orderid = $row['max_value'] + 1;

        // Get delivery method and payment method from the form
        $deliveryMethod = isset($_POST['deliveryMethod']) ? $_POST['deliveryMethod'] : 'default_delivery_method';
        $paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : 'default_payment_method';
        
        // Calculate the total price (assuming you have this value from the previous page)
        $total_price = isset($_POST['total_price']) ? $_POST['total_price'] : 0;
        
        // Set the default values for status, address, and order time
        $status = '未出貨';
        $address = 'taiwan, taipei';
        $orderTime = date('Y-m-d H:i:s'); // Current date and time

        // Insert data into ORDER_INCLUDE table
        // Get the product data from the form
        
        $sqlOrder = "INSERT INTO orders (orderid, buyerid, status, address, delivery_method, payment_method, order_time, total_price)
                    VALUES (:orderid, :userid, :status, :address, :deliveryMethod, :paymentMethod, :orderTime, :total_price)";   
        
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->bindParam(':orderid', $orderid, PDO::PARAM_INT);
        $stmtOrder->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmtOrder->bindParam(':status', $status);
        $stmtOrder->bindParam(':address', $address);
        $stmtOrder->bindParam(':deliveryMethod', $deliveryMethod);
        $stmtOrder->bindParam(':paymentMethod', $paymentMethod);
        $stmtOrder->bindParam(':orderTime', $orderTime);
        $stmtOrder->bindParam(':total_price', $total_price);
        $stmtOrder->execute();

        echo "hi";
        //加上新order_include
        $sqlOrderInclude = "INSERT INTO ORDER_INCLUDE (orderid, productid, product_count)
                                VALUES (:orderid, :productid, :product_count)";    
        $stmtOrderInclude = $pdo->prepare($sqlOrderInclude);

        foreach ($productData as $product) {
            
            $productid = $product['productId'];
            $product_count = $product['productCount'];
            // Assuming $orderId is the ID of the order you obtained earlier
            $stmtOrderInclude->bindParam(':productid', $productid, PDO::PARAM_INT);
            $stmtOrderInclude->bindParam(':orderid', $orderid, PDO::PARAM_INT);
            $stmtOrderInclude->bindParam(':product_count', $product_count, PDO::PARAM_INT);
            $stmtOrderInclude->execute();
        }
        //更新購物車
        $sql = "UPDATE Add_to_cart
                SET status = false, product_count = 0
                where userid = :userid and productid = :productid";    
    
        $stmt= $pdo->prepare($sql);

        // echo 'Count:',$count;
        foreach ($productData as $product) {
            $productid = $product['productId'];
            $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $stmt->bindParam(':productid', $productid, PDO::PARAM_INT);
            $stmt->execute();
        }
        

        echo 'Order placed successfully!','<br>' ;
        header("Location: myorder.php");
        $pdo->commit();


    }
    catch (PDOException $e) {
        // Handle database connection errors
        $pdo->rollBack();
        echo 'Order placed failed: ' . $e->getMessage();
    }
    finally {
        // Close the database connection
        $pdo = null;
    }
} else {
    // Redirect to the cart page if accessed without a POST request
    header("Location: cartPage.php");
    exit();
}

