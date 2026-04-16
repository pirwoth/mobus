<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = (int)$_SESSION['user_id'];
$bus_id = (int)($_GET['id'] ?? 0);

if ($bus_id) {
    mysqli_query($conn, "DELETE FROM buses WHERE id = $bus_id AND created_by_operator = $operator_id");

    if (mysqli_affected_rows($conn) > 0) {
        header("Location: dashboard.php?msg=Bus+deleted+successfully");
    }
    else {
        header("Location: dashboard.php?msg=Bus+not+found+or+could+not+be+deleted");
    }
}
else {
    header("Location: dashboard.php");
}
exit;

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. SECURE DELETION:
 * We ensure that a bus can only be deleted if its `created_by_operator` matches 
 * the ID of the logged-in session.
 * 
 * 2. CASCADE ACTIONS:
 * In the database schema, we have set up "ON DELETE CASCADE". This means if 
 * a bus is deleted, all its associated Seats and Trips are automatically 
 * removed by MySQL.
 * 
 * 3. FEEDBACK:
 * We use mysqli_affected_rows() to verify if the deletion actually happened 
 * before showing a success message to the user.
 */
?>