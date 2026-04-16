<?php
header('Content-Type: application/json');
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$trip_id = (int)($_GET['trip_id'] ?? 0);

if (empty($trip_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing trip_id parameter']);
    exit;
}

$sql = "SELECT seat_number FROM bookings WHERE trip_id = $trip_id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed']);
} else {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = (int)$row['seat_number'];
    }
    echo json_encode($rows);
}

/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. POLLING DATA:
 * This API is called every 5 seconds by the 'book.php' page. It returns a list 
 * of seat numbers that are already taken.
 * 
 * 2. CASTING TO INT:
 * We use (int) on the results to make sure the seat numbers are actual 
 * numbers in the JSON output, not strings.
 * 
 * 3. SECURITY:
 * We cast $trip_id to an integer using (int) immediately. This is a very 
 * fast and secure way to prevent SQL injection for ID fields.
 */
?>