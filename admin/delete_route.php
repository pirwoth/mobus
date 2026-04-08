<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$route_id = $_GET['id'] ?? 0;

if ($route_id) {
    // Only delete global routes
    $stmt = $pdo->prepare("DELETE FROM routes WHERE id = ? AND created_by_operator IS NULL");
    $stmt->execute([$route_id]);
}

header("Location: routes.php?msg=Route+deleted+successfully");
exit;
?>