<?php
$host = '127.0.0.1';
$dbname = 'mobus';
$username = 'root';
$password = ''; // Default XAMPP/WAMP password is usually empty, or 'root' for MAMP. Change as needed.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$base_url = strpos($_SERVER['PHP_SELF'], '/mobus/') === 0 ? '/mobus' : '';
define('BASE_URL', $base_url);
?>