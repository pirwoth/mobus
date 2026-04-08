<?php
session_start();

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function checkRole($requiredRole)
{
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }

    if ($_SESSION['role'] !== $requiredRole) {
        // Redirect unauthorized users to their appropriate dashboard if logged in,
        // or just a general access denied page.
        // For simplicity, we redirect to login to reset their session or handle it there.
        // Better logic: redirect based on their actual role.
        redirectUserToDashboard($_SESSION['role']);
        exit();
    }
}

function redirectUserToDashboard($role)
{
    switch ($role) {
        case 'admin':
            header("Location: " . BASE_URL . "/admin/dashboard.php");
            break;
        case 'operator':
            header("Location: " . BASE_URL . "/operator/dashboard.php");
            break;
        case 'passenger':
            header("Location: " . BASE_URL . "/passenger/app.php");
            break;
        case 'verifier':
            header("Location: " . BASE_URL . "/verifier/app.php");
            break;
        default:
            header("Location: " . BASE_URL . "/login.php");
            break;
    }
    exit();
}
?>