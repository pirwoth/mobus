<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = (int)$_SESSION['user_id'];
$trip_id = (int)($_GET['id'] ?? 0);

if ($trip_id) {
    mysqli_query($conn, "DELETE FROM trips WHERE id = $trip_id AND created_by_operator = $operator_id");

    if (mysqli_affected_rows($conn) > 0) {
        header("Location: trips.php?msg=Trip+deleted+successfully");
    }
    else {
        header("Location: trips.php?msg=Trip+not+found+or+could+not+be+deleted");
    }
}
else {
    header("Location: trips.php");
}
exit;

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. SECURE DELETION:
 * We use `AND created_by_operator = $operator_id` in the SQL query. This is a 
 * security measure to ensure an operator can only delete their own trips, 
 * even if they try to hack the URL by changing the ID.
 * 
 * 2. FEEDBACK:
 * We use mysqli_affected_rows($conn) to check if any row was actually deleted. 
 * If it returns 0, it means either the trip didn't exist or it didn't belong 
 * to that operator.
 * 
 * 3. CASTING TO INT:
 * We cast both $operator_id and $trip_id to integers. This makes the SQL query 
 * safe and faster.
 */
?>