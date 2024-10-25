<?php
session_start(); // Start the session

// Unset all of the session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the admin login page
header('Location: AdminLogin.php');
exit();
?>
