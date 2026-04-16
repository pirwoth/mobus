<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$ticket_id = (int)($_GET['id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if (!$ticket_id) {
    header("Location: tickets.php");
    exit;
}

$sql = "SELECT tkt.*, b.ticket_number, b.seat_number, b.id as booking_id,
               tr.departure_time, tr.travel_date, 
               bus.bus_name, bus.bus_number, 
               r.origin, r.destination, 
               u.name as passenger_name
        FROM tickets tkt
        JOIN bookings b ON tkt.booking_id = b.id
        JOIN trips tr ON b.trip_id = tr.id
        JOIN buses bus ON tr.bus_id = bus.id
        JOIN routes r ON tr.route_id = r.id
        JOIN users u ON b.user_id = u.id
        WHERE tkt.id = $ticket_id AND b.user_id = $user_id";

$result = mysqli_query($conn, $sql);
$ticket = mysqli_fetch_assoc($result);

if (!$ticket) {
    die("Ticket not found or unauthorized.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Ticket - <?= htmlspecialchars($ticket['ticket_number'])?></title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System</h2>
        <div class="nav-links">
            <a href="app.php">Find a Bus</a>
            <a href="tickets.php">My Tickets</a>
        </div>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <a href="tickets.php" class="back-link">&larr; Back to My Tickets</a>

        <div class="ticket-card">
            <div class="ticket-header">
                <h1>Digital Boarding Pass</h1>
                <p><?= htmlspecialchars($ticket['origin'])?> to <?= htmlspecialchars($ticket['destination'])?></p>
            </div>

            <div class="ticket-details">
                <div class="detail-group">
                    <div class="detail-label">Passenger Name</div>
                    <div class="detail-value"><?= htmlspecialchars($ticket['passenger_name'])?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Ticket Number</div>
                    <div class="detail-value"><?= htmlspecialchars($ticket['ticket_number'])?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Travel Date</div>
                    <div class="detail-value"><?= date('F j, Y', strtotime($ticket['travel_date']))?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Departure Time</div>
                    <div class="detail-value"><?= date('h:i A', strtotime($ticket['departure_time']))?></div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Bus Name</div>
                    <div class="detail-value"><?= htmlspecialchars($ticket['bus_name'])?> (<?= htmlspecialchars($ticket['bus_number'])?>)</div>
                </div>

                <div class="detail-label">Seat Number</div>
                <div class="detail-value" style="font-size: 28px; color: var(--accent);">
                    <?= htmlspecialchars($ticket['seat_number'])?>
                </div>
            </div>

            <div class="qr-section">
                <img src="<?= htmlspecialchars($ticket['qr_code_url'])?>" alt="QR Code" class="qr-code">
                <p style="color: #888; font-size: 14px; margin: 0;">Scan to verify boarding</p>
                <p style="color: #aaa; font-size: 12px; margin-top: 5px;">Booking ID: <?= $ticket['booking_id']?></p>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. DISPLAYING TICKETS:
 * This page shows the final boarding pass for a passenger. It pulls data from 
 * Tickets, Bookings, Trips, Buses, Routes, and Users tables.
 * 
 * 2. SECURITY CHECK:
 * We use `WHERE tkt.id = $ticket_id AND b.user_id = $user_id`. The second part 
 * is crucial! It ensures that a passenger cannot just change the ID in the 
 * URL to see someone else's ticket.
 * 
 * 3. FORMATTING DATE/TIME:
 * We use PHP's `date()` and `strtotime()` functions to turn the database format 
 * (2024-01-01) into a more readable student-friendly format (January 1, 2024).
 * 
 * 4. QR CODE:
 * The QR code URL was generated during payment and stored in the database. 
 * We simply display it here as an image.
 */
?>