<?php
// include('config.php');
// session_start();

// if (isset($_POST['productid'])) {
//     // Get the product ID from the form
//     $productid = $_POST['productid'];
    
//     // Get the user ID from the session
//     $userid = $_SESSION['userid'];
    
//     $sql = "SELECT * FROM add_to_cart WHERE userid = :userid and productid = :productid";
//     $stmt = $pdo->prepare($sql);
//     $stmt->bindParam(':userid', $userid);
//     $stmt->bindParam(':productid', $productid);
//     $stmt->execute();
//     $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     if(is_array($cart) && !empty($cart))
//     {
//         $product_count = $cart[0]['product_count'];
//         $sql = "UPDATE add_to_cart SET product_count = :product_count + 1 WHERE userid = :userid and productid = :productid";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindParam(':userid', $userid);
//         $stmt->bindParam(':productid', $productid);
//         $stmt->bindParam(':product_count', $product_count);
//         $stmt->execute();
//         header("Location: product_page.php");
//         exit();
        
//     }
//     else{
//         // Insert a new row into the 'add_to_cart' table
//         $sql = "INSERT INTO add_to_cart (userid, productid,product_count, status) VALUES (:userid, :productid, 1, true)";
        
//         try {
//             $stmt = $pdo->prepare($sql);
//             $stmt->bindParam(':userid', $userid);
//             $stmt->bindParam(':productid', $productid);
//             $stmt->execute();
            
//             // Redirect back to the product page after adding to cart
//             header("Location: product_page.php");
//             exit();
//         } catch (PDOException $e) {
//             die('Query failed: ' . $e->getMessage());
//         }
//     }

    
// } 
?>

<?php
include('config.php');
session_start();

if (isset($_POST['productid'])) {
    // Get the product ID from the form
    $productid = $_POST['productid'];
    
    // Get the user ID from the session
    $userid = $_SESSION['userid'];
    
    // Insert a new row into the 'add_to_cart' table
    $sql = "INSERT INTO add_to_cart (userid, productid,product_count, status) VALUES (:userid, :productid, 1, true)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':productid', $productid);
        $stmt->execute();
        
        // Redirect back to the product page after adding to cart
        header("Location: product_page.php");
        exit();
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}
?>

