<?php
// Determine if we are running locally (XAMPP) or on a live server
$server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';

$is_local = ($server_name == 'localhost' || $server_name == '127.0.0.1'
    || preg_match('/^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[01])\.)/', $server_name));

if ($is_local) {
    // Local XAMPP credentials
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nsbm_sportshub";
} else {
    // Live Production Server credentials
    $host = "sql300.infinityfree.com";
    $username = "if0_43388055";
    $dbname = "if0_43388055_nsbm_sportshub";
}


$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>