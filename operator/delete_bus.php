<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$bus_id = $_GET['id'] ?? 0;

if ($bus_id) {
    // Attempt delete only if the bus belongs to this operator
    $stmt = $pdo->prepare("DELETE FROM buses WHERE id = ? AND created_by_operator = ?");
    $stmt->execute([$bus_id, $operator_id]);

    if ($stmt->rowCount() > 0) {
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
?>