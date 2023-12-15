<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="stylesheet" href="style1.css">

</head>
<body>
    <h1>Welcome to TradeOrbit!</h1>
    <h2>Register</h2>
    <div class = "container">
    <form action="register.php" method="post">
        <label for="email">Email:</label>
        <input type="email" class = "underLineStyle inputPaddingLeft" name="email" required><br>
        
        <label for="password">Password:</label>
        <input type="password" class = "underLineStyle inputPaddingLeft" name="password" required><br>
        
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" class = "underLineStyle inputPaddingLeft"  name="confirm_password" required><br>

        <label for="phone">Phone:</label>
        <input type="text" class = "underLineStyle inputPaddingLeft"  name="phone" required><br>

        <!-- <label for="role">Role:</label>
        <input type="text" class = "underLineStyle" name="role" required><br> -->

        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
    
</body>
</html>

<?php
// Include the database configuration
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $phone = $_POST["phone"];

    // SQL query to select the maximum value of a column
    $get_userID_sql = "SELECT MAX(CAST(userid AS INTEGER)) AS max_value FROM Users";

    // Execute the query
    $result = $pdo->query($get_userID_sql);

    // Fetch the result as an associative array
    $row = $result->fetch(PDO::FETCH_ASSOC);

    // Get the maximum value
    $maxValue = $row['max_value'] + 1;
    // Output the result or use it as needed
    echo "The maximum value is: $maxValue";

    // Prepare and execute the SQL query
    $sql = "INSERT INTO users (userid, password, phone_number, email,role) VALUES (:maxValue, :password, :phone, :email, 'User')";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters and execute the statement
    $stmt->bindParam(':maxValue', $maxValue, PDO::PARAM_INT);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Check for success
    if ($stmt->rowCount() > 0) {
        echo "Registration successful!";
        header("Location: login.php");
    } else {
        echo "Registration failed. Please try again.";
    }

    // Close the statement
    $stmt->closeCursor();
} else {
    // Redirect to the registration page if accessed directly
    // header("Location: register.php");
    exit();
}

// Close the database connection
$pdo = null;
?>
