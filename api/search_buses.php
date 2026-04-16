<?php
header('Content-Type: application/json');
require_once '../config/db.php';

$origin = mysqli_real_escape_string($conn, $_GET['origin'] ?? '');
$destination = mysqli_real_escape_string($conn, $_GET['destination'] ?? '');
$travel_date = mysqli_real_escape_string($conn, $_GET['travel_date'] ?? '');

if (empty($origin) || empty($destination) || empty($travel_date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$sql = "SELECT 
            t.id AS 'Trip ID',
            b.bus_name AS 'Bus Name',
            b.bus_number AS 'Bus Number',
            t.departure_time AS 'Departure Time',
            t.price AS 'Price',
            b.total_seats AS 'Total Seats',
            (b.total_seats - (SELECT COUNT(*) FROM bookings WHERE trip_id = t.id AND status IN ('pending', 'paid'))) AS 'Remaining Seats'
        FROM trips t
        JOIN routes r ON t.route_id = r.id
        JOIN buses b ON t.bus_id = b.id
        WHERE r.origin = '$origin' 
          AND r.destination = '$destination' 
          AND t.travel_date = '$travel_date'
        ORDER BY t.departure_time ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
} else {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    echo json_encode($rows);
}

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. JSON API:
 * This file returns data in JSON format instead of HTML. This allows our 
 * JavaScript code in 'app.php' to read the data easily.
 * 
 * 2. SUBQUERY FOR SEATS:
 * Inside the main SQL query, we use a "Subquery" to calculate 'Remaining Seats'. 
 * It counts how many bookings exist for that trip and subtracts them from the 
 * total bus capacity.
 * 
 * 3. ESCAPING:
 * Since values come from the URL ($_GET), we use mysqli_real_escape_string() 
 * to prevent any malicious SQL entered into the search boxes.
 */
?>