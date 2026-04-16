<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = (int)$_SESSION['user_id'];
$route_id = (int)($_GET['id'] ?? 0);

if ($route_id) {
    mysqli_query($conn, "DELETE FROM routes WHERE id = $route_id AND created_by_operator = $operator_id");
}

header("Location: routes.php?msg=Route+deleted+successfully");
exit;

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. SECURE DELETION:
 * Just like with trips, we check that the route belongs to the operator 
 * (`created_by_operator = $operator_id`) before deleting. This prevents 
 * an operator from deleting Global routes or routes belonging to others.
 * 
 * 2. CASCADE DELETION:
 * In a real database, deleting a route will also delete all trips that use 
 * that route. This is handled by "FOREIGN KEY" constraints in MySQL.
 */
?>