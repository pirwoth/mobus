<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$staff_id = (int)($_GET['id'] ?? 0);

if ($staff_id) {
    // We only allow deleting Operators and Verifiers from here, NOT other Admins or Passengers
    $sql = "DELETE FROM users WHERE id = $staff_id AND role IN ('operator', 'verifier')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: create_user.php?msg=Staff+member+deleted+successfully");
    } else {
        header("Location: create_user.php?msg=Error+deleting+staff+member");
    }
} else {
    header("Location: create_user.php");
}
exit;

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. SECURE DELETION:
 * We use the WHERE clause to ensure only staff roles ('operator', 'verifier') can 
 * be deleted. This prevents an admin from accidentally deleting themselves 
 * or a passenger from this specific page.
 * 
 * 2. CASTING:
 * Just like before, we cast the ID to an (int) for database safety.
 */
?>
