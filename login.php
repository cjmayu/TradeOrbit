<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h1>Welcome to TradeOrbit!</h1>
    <h2>Login</h2>
    <div class = "container">
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="text" class = "underLineStyle inputPaddingLeft" name="email" required><br>
        
        <label for="password">Password:</label>
        <input type="password" class = "underLineStyle inputPaddingLeft" name="password" required><br>
        
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>



<?php
include('config.php');
session_start(); // Start the session


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"];
    echo "Hi" . $email;
    echo "Hi" . $password;
    // Prepare and execute the SQL query
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters and execute the statement
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    // Get the result set
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if the user exists
    if (count($results) > 0) {
        $row = $results[0]; // Fetch the first result
        // $hashedPassword = $row['password'];
        echo 'here ' . $row['password'];
        // Verify the password
        if ($password == $row['password']) {
            echo "Login successful! Welcome, $email!";
            $_SESSION['userid'] = $row['userid'];

            if($row['role'] == 'User')
            {
                header("Location: product_page.php");
            }
            else
            {
                header("Location: admin_product_page.php");
            }
            
        } else {
            echo "Login failed. Incorrect password.";
        }
    } else {
        echo "Login failed. User not found.";
    }

    // Close the statement and result set
    $stmt->closeCursor(); // Instead of $stmt->close()
} else {
    // Redirect to the login page if accessed directly
    //header("Location: login.php");
    exit();
}

// Close the database connection
$pdo = null; // Instead of $conn->close()
?>
