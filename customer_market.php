<?php
// Database connection parameters
include('config.php');
session_start();
$user_id = $_SESSION['userid'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the follow button click
    if (isset($_POST['follow'])) {
        $storeID = $_POST['storeID']; // Get store ID from the form
        
        // Insert into the FOLLOW table
        $stmt = $pdo->prepare("INSERT INTO Follow (user_id, store_id) VALUES (?, ?)");
        $stmt->bindParam(':uer_id', $user_id);
        $stmt->bindParam(':store_id', $storeID);
        $stmt->execute();
    }
}

// Fetch and display all stores
$stmt = $pdo->query("SELECT Store_name FROM Store");
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store List</title>
</head>
<body>

<h2>Store List</h2>

<?php foreach ($stores as $store): ?>
    <div>
        <p><?= $store['Store_name'] ?></p>
        <form method="post" action="">
            <input type="hidden" name="storeID" value="<?= $store['store_id'] ?>">
            <button type="submit" name="follow">Follow</button>
        </form>
    </div>
<?php endforeach; ?>

</body>
</html>
