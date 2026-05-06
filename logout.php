<?php
session_start(); // Access the current session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session on the server

header("Location: login.php");
exit();
?>