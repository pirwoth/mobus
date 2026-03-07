<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$trip_id = $_GET['id'] ?? 0;

if ($trip_id) {
    // Attempt delete only if the trip belongs to this operator
    $stmt = $pdo->prepare("DELETE FROM trips WHERE id = ? AND created_by_operator = ?");
    $stmt->execute([$trip_id, $operator_id]);

    if ($stmt->rowCount() > 0) {
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
?>