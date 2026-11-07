<?php
// Determine if we are running locally (XAMPP) or on a live server
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
// localhost, or a private Wi-Fi/LAN address (so you can test from a phone: http://192.168.x.x/nsbm_sportshub/)
$is_local = ($server_name == 'localhost' || $server_name == '127.0.0.1'
    || preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[01])\.)/', $server_name));

if ($is_local) {
    // Local XAMPP credentials (default root with no password)
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nsbm_sportshub"; // Change to your exact local database name if different
} else {
    // Live Production Server credentials (update these when you host live)
    $host = "sqlxxx.yourhostingprovider.com"; // Provided by your web host
    $username = "your_live_db_user";          // Provided by your web host
    $password = "YourSecureLivePassword123";  // Your actual secure database password
    $dbname = "your_live_db_name";            // Provided by your web host
}

// Create database connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set character set to UTF-8
$conn->set_charset("utf8");
?>