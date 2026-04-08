<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$ticket_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

if (!$ticket_id) {
    header("Location: tickets.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT tkt.*, b.ticket_number, b.seat_number, b.id as booking_id,
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
    WHERE tkt.id = ? AND b.user_id = ?
");
$stmt->execute([$ticket_id, $user_id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found or unauthorized.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Ticket -
        <?= htmlspecialchars($ticket['ticket_number'])?>
    </title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - App</h2>
        <div class="nav-links">
            <a href="app.php" class="nav-link">Search</a>
            <a href="tickets.php" class="nav-link">My Tickets</a>
        </div>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <a href="tickets.php" class="back-link">&larr; Back to My Tickets</a>

        <div class="ticket-card">
            <div class="ticket-header">
                <h1>Digital Boarding Pass</h1>
                <p>
                    <?= htmlspecialchars($ticket['origin'])?> to
                    <?= htmlspecialchars($ticket['destination'])?>
                </p>
            </div>

            <div class="ticket-details">
                <div class="detail-group">
                    <div class="detail-label">Passenger Name</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($ticket['passenger_name'])?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Ticket Number</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($ticket['ticket_number'])?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Travel Date</div>
                    <div class="detail-value">
                        <?= date('F j, Y', strtotime($ticket['travel_date']))?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Departure Time</div>
                    <div class="detail-value">
                        <?= date('h:i A', strtotime($ticket['departure_time']))?>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Bus Details</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($ticket['bus_name'])?> (
                        <?= htmlspecialchars($ticket['bus_number'])?>)
                    </div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Seat Number</div>
                    <div class="detail-value" style="font-size: 24px; color: #007bff;">
                        <?= htmlspecialchars($ticket['seat_number'])?>
                    </div>
                </div>
            </div>

            <div class="qr-section">
                <img src="<?= htmlspecialchars($ticket['qr_code_url'])?>" alt="QR Code" class="qr-code">
                <p style="color: #888; font-size: 14px; margin: 0;">Scan to verify boarding</p>
                <p style="color: #aaa; font-size: 12px; margin-top: 5px;">Booking ID:
                    <?= $ticket['booking_id']?>
                </p>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>