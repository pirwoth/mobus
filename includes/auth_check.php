<?php
/**
 * Authentication Helper Functions
 * These functions help us track if a user is logged in and what they are allowed to see.
 */
session_start(); // Start the session to access $_SESSION variables

/**
 * Check if a user is logged in
 * Returns true if the user has a user_id in their session.
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Protected Page Security
 * Redirects the user if they don't have the required role.
 */
function checkRole($requiredRole)
{
    // If NOT logged in, send them back to the login page
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }

    // If their role doesn't match the required role for this page
    if ($_SESSION['role'] !== $requiredRole) {
        // Find out where they SHOULD be based on their actual role
        redirectUserToDashboard($_SESSION['role']);
        exit();
    }
}

/**
 * Dashboard Redirector
 * Sends the user to the correct home page based on who they are.
 */
function redirectUserToDashboard($role)
{
    if ($role === 'admin') {
        header("Location: " . BASE_URL . "/admin/dashboard.php");
    } 
    elseif ($role === 'operator') {
        header("Location: " . BASE_URL . "/operator/dashboard.php");
    } 
    elseif ($role === 'passenger') {
        header("Location: " . BASE_URL . "/passenger/app.php");
    } 
    elseif ($role === 'verifier') {
        header("Location: " . BASE_URL . "/verifier/app.php");
    } 
    else {
        // Default fallback if role is unknown
        header("Location: " . BASE_URL . "/login.php");
    }
    exit();
}
?>