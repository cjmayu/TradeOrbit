<?php

$host = 'localhost';
$port = 5433; // remember to replace your own connection port
$dbname = 'FP_1116'; // remember to replace your own database name 
$user = 'postgres'; // remember to replace your own username 
$password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

// Create a database connection
$pdo = null;
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}

?>