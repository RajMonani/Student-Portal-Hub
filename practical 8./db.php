<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db   = 'StudentHub';
$user = 'root'; 
$pass = '';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
    PDO::ATTR_EMULATE_PREPARES   => false,                  
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px; padding: 20px; border: 2px solid green; border-radius: 10px; display: inline-block;'>";
    echo "<h2 style='color: green;'>✅ Database Connection Successful!</h2>";
    echo "<p>Secure PDO connection to the <strong>StudentHub</strong> database has been established.</p>";
    echo "</div>";

} catch (\PDOException $e) {
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px; padding: 20px; border: 2px solid red; border-radius: 10px; display: inline-block;'>";
    echo "<h2 style='color: red;'>❌ Database Connection Failed</h2>";
    echo "<p>Error message: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please ensure XAMPP MySQL is running and the database is imported.</p>";
    echo "</div>";
}
?>