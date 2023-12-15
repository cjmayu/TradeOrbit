<?php
include('config.php');
session_start();
$owner_id = $_SESSION['userid'];
$productid = isset($_GET['productid']) ? urldecode($_GET['productid']) : null;
// Fetch product details for editing
if ($productid) {

    
    try {
        $get_product_query = "SELECT Product_name, Price, Item_count, storeid, status
                                FROM Product
                                WHERE productid = :product_id AND storeid IN 
                                (SELECT storeid 
                                FROM Store 
                                WHERE Ownerid = :owner_id)";

        $stmt = $pdo->prepare($get_product_query);
        $stmt->bindParam(':product_id', $productid);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $product['storeid'];
    } catch (PDOException $e) {
        die('Query failed: ' . $e->getMessage());
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $new_status = $_POST['status'];
    $new_stock = $_POST['new_stock'];
    $new_price = $_POST['price'];
    $new_name = $_POST['product_name'];

    // Update the status and remaining stock in the product table
    $update_product_query = "UPDATE product SET status = :new_status, item_count = :new_stock, product_name = :product_name, price = :price WHERE productid = :product_id";
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare($update_product_query);
        $stmt->bindParam(':product_id', $productid, PDO::PARAM_INT);
        $stmt->bindParam(':new_status', $new_status, PDO::PARAM_STR);
        $stmt->bindParam(':new_stock', $new_stock, PDO::PARAM_INT);
        $stmt->bindParam(':price', $new_price, PDO::PARAM_STR);
        $stmt->bindParam(':product_name', $new_name, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();
        header("Location: seller_market.php?storeid=" . urlencode($product['storeid']));
        // Optionally, you may want to redirect or refresh the page after the update
        // header("Location: " . $_SERVER['PHP_SELF'] . '?storeid=' . urlencode($storeid));
    } catch (PDOException $e) {
        // An error occurred, rollback the transaction
        $pdo->rollBack();
        die('Update failed: ' . $e->getMessage());
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style1.css">
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        /* Add your CSS styles here */

        .edit-product-form {
            margin-top: 20px;
        }

        .form-field {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h2>Edit Product</h2>
    <form method="post">
        <input type="hidden" name="storeid" value=<?php echo $product['storeid']; ?>>
        <input type='hidden' name='productid' value=<?php echo $productid; ?>>
        <div class="form-field">
            <label for="product_name">Product Name:</label>
            <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required>
        </div>
        <div class="form-field">
            <label for="price">Price:</label>
            <input type="text" name="price" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="form-field">
            <label for="item_count">Stock:</label>
            <input type="number" name="new_stock" value="<?php echo $product['item_count']; ?>" required>
        </div>
        <div class="form-field">
            <label for="status">Status:</label>
            <select name="status" required>
                <option value="1" <?php echo ($product['status'] === '1') ? 'selected' : ''; ?>>Available</option>
                <option value="0" <?php echo ($product['status'] === '0') ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </div>
        <div class="form-field">
            <button type="submit" name="edit_product">Save Changes</button>
        </div>
    </form>
</body>
</html>
