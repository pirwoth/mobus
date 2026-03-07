<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$origin = $_GET['origin'] ?? '';
$destination = $_GET['destination'] ?? '';
$travel_date = $_GET['travel_date'] ?? '';

if (empty($origin) || empty($destination) || empty($travel_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters: origin, destination, and travel_date']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            b.bus_name AS 'Bus Name',
            b.bus_number AS 'Bus Number',
            t.departure_time AS 'Departure Time',
            t.price AS 'Price',
            b.total_seats AS 'Available Seats'
        FROM trips t
        JOIN routes r ON t.route_id = r.id
        JOIN buses b ON t.bus_id = b.id
        WHERE r.origin = :origin 
          AND r.destination = :destination 
          AND t.travel_date = :travel_date
        ORDER BY t.departure_time ASC
    ");

    $stmt->execute([
        'origin' => $origin,
        'destination' => $destination,
        'travel_date' => $travel_date
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
}
catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error query failed']);
}
?>