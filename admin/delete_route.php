<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$route_id = (int)($_GET['id'] ?? 0);

if ($route_id) {
    mysqli_query($conn, "DELETE FROM routes WHERE id = $route_id AND created_by_operator IS NULL");
}

header("Location: routes.php?msg=Route+deleted+successfully");
exit;

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. DELETING DATA:
 * We use the SQL DELETE command to remove a route from the database.
 * 
 * 2. PROTECTION:
 * We add `AND created_by_operator IS NULL` to the query to ensure that the 
 * Admin can only delete "Global" routes from this specific page.
 * 
 * 3. RETURNING:
 * After deleting, we use header("Location: ...") to send the Admin back 
 * to the routes list with a success message.
 */
?>