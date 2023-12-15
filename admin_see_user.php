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
     <h1>TradeOrbit</h1>
     <nav>
        <ul class="flex-nav">
            <li class="active"><a href="admin_product_page.php">Home page</a></li>
            <li class="active"><a href="admin_page.php">My profile</a></li>
            <li class="active"><a href="admin_see_user.php">User and Market Information</a></li>
        </ul>
    </nav>

<?php
// Database connection
include('config.php');

// Check if the form is submitted for deletion
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['delete_user'];

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete user from the 'user' table
        $delete_user_query = "DELETE FROM users WHERE userid = :userid";
        $stmt = $pdo->prepare($delete_user_query);
        $stmt->bindParam(':userid', $user_id_to_delete);
        $stmt->execute();

        // Delete associated market from the 'store' table
        $delete_market_query = "DELETE FROM store WHERE ownerid = :userid";
        $stmt = $pdo->prepare($delete_market_query);
        $stmt->bindParam(':userid', $user_id_to_delete);
        $stmt->execute();

        // Commit the transaction
        $pdo->commit();

    } catch (PDOException $e) {
        // An error occurred, rollback the transaction
        $pdo->rollBack();
        die('Transaction failed: ' . $e->getMessage());
    }
}

// Fetch users and their markets
$productsPerPage = 120;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $productsPerPage;


// Query to fetch products with pagination
$sql = "SELECT users.userid, users.email, store.store_name
          FROM users
          LEFT JOIN store ON users.userid = store.ownerid
          Order by email LIMIT $productsPerPage OFFSET $offset";
// $stmt = $pdo->prepare($sql);
// $stmt->execute();

try {
    $stmt = $pdo->query($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}



// Display users and markets with delete button
echo '<h2>User and Market Information</h2>';
echo '<table border="1">';
echo '<tr><th>User ID</th><th>Username</th><th>Market</th><th>Action</th></tr>';

foreach ($result as $row) {
    echo '<tr>';
    echo '<td>' . $row['userid'] . '</td>';
    echo '<td>' . $row['email'] . '</td>';
    echo '<td>' . ($row['store_name'] ? $row['store_name'] : 'No store') . '</td>';
    echo '<td>';
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="delete_user" value="' . $row['userid'] . '">';
    echo '<button type="submit">Delete</button>';
    echo '</form>';
    echo '</td>';
    echo '</tr>';
}

echo '</table>';
?>

<div class = 'Pagination'>
        <?php
        // Calculate the total number of pages

        $sql = "SELECT Count(*) FROM users";
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters and execute the statement
        //$stmt->bindParam(":email", $email);
        $stmt->execute();
    
        // Get the result set
        $totalUsers = $stmt->fetchColumn();
        $usersPerPage = 100;
        $totalPages = ceil($totalUsers / $productsPerPage);

        // Display pagination links
        if ($current_page > 1) {
            echo "<a href='?page=" . ($current_page - 1) . "'>Previous</a> ";
        }

        for ($i = max(1, $current_page - 2); $i <= min($current_page + 2, $totalPages); $i++) {
            echo "<a href='?page=$i'>$i</a> ";
        }

        if ($current_page < $totalPages) {
            echo "<a href='?page=" . ($current_page + 1) . "'>Next</a>";
        }
        ?>
</div>

</body>
</html>