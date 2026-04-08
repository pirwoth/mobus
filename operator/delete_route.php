<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$route_id = $_GET['id'] ?? 0;

if ($route_id) {
    // Only delete if the route was created by THIS operator
    $stmt = $pdo->prepare("DELETE FROM routes WHERE id = ? AND created_by_operator = ?");
    $stmt->execute([$route_id, $operator_id]);
}

header("Location: routes.php?msg=Route+deleted+successfully");
exit;
?>