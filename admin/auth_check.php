<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Unauthorized access: Redirect them to the login page or an error page
    header("Location: ../login.php?error=unauthorized");
    exit();
}
?>