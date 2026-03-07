<?php
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../includes/auth_check.php';

// Allow passengers to access this endpoint
checkRole('passenger');

$trip_id = $_GET['trip_id'] ?? 0;

if (empty($trip_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing trip_id parameter']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT seat_number FROM bookings WHERE trip_id = ?");
    $stmt->execute([$trip_id]);
    $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Return array of booked seat numbers
    echo json_encode($results);
}
catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
}
?>