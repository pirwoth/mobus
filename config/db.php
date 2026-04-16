<?php
$host = '127.0.0.1';
$dbname = 'mobus';
$username = 'root';
$password = '';

// Step 1: Establish connection using MySQLi (Procedural)
$conn = mysqli_connect($host, $username, $password, $dbname);

// Step 2: Check if the connection worked
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Step 3: Define Base URL for project navigation
$base_url = strpos($_SERVER['PHP_SELF'], '/mobus/') === 0 ? '/mobus' : '';
define('BASE_URL', $base_url);

/**
 * --- DOCUMENTATION & EXPLANATIONS ---
 * 
 * 1. DATABASE CONNECTION:
 * We use mysqli_connect() instead of PDO because it is simpler and what most students learn.
 * $conn is our "key" to the database. We must pass it whenever we want to run a query.
 * 
 * 2. CONNECTION ERROR CHECK:
 * If the database details are wrong, mysqli_connect_error() tells us exactly what happened.
 * 
 * 3. BASE_URL:
 * This constant helps us link files (like CSS or JS) correctly from any folder.
 */
?>