<?php
$host = 'localhost';
$db   = 'iot';  // Your PostgreSQL database name
$user = 'postgres';  // Your PostgreSQL user
$pass = 'N@imspry2952003';  // Your password
$port = 5432;  // Default PostgreSQL port

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
