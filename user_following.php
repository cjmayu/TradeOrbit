<?php
// Database connection
include('config.php');
session_start();
$userid = $_SESSION['userid']; // Replace with the actual seller_id
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $userid?>'s following stores</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <!--上面選單 -->
    <h1>User and Followed Stores Information</h1>
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

// Fetch user names and stores they follow
$query = "SELECT s.storeid, s.store_name
          FROM store s
            JOIN follow f ON s.storeid = f.storeid
          WHERE f.userid = :userid";

try {
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Query failed: ' . $e->getMessage());
}

// Display user names and followed stores
//echo '<h2>User and Followed Stores Information</h2>';
echo '<table border="1">';
echo '<tr><th>Followed Stores name</th></tr>';

foreach ($result as $row) {
    echo '<tr>';
    //echo '<td>' . $row['store_name'] . '</td>';
    echo "<td><a href = 'customer_view_one_market.php?storeid=${row['storeid']}'  .  >{$row['store_name'] }</a></td>";
    // echo '<td>' . ($row['followed_stores'] ? $row['followed_stores'] : 'No Stores Followed') . '</td>';
    echo '</tr>';
}

echo '</table>';
?>
</body>
</html>
