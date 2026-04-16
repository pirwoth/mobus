<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$user_id = $_SESSION['user_id'];

// Fetch user's tickets (bookings with 'paid' status)
$sql = "SELECT tkt.id as ticket_id, b.ticket_number, b.seat_number, 
               tr.departure_time, tr.travel_date, r.origin, r.destination, b.status
        FROM tickets tkt
        JOIN bookings b ON tkt.booking_id = b.id
        JOIN trips tr ON b.trip_id = tr.id
        JOIN routes r ON tr.route_id = r.id
        WHERE b.user_id = $user_id
        ORDER BY tr.travel_date DESC, tr.departure_time DESC";

$result = mysqli_query($conn, $sql);
$tickets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tickets[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - Mobus</title>
    
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
            <a href="app.php" class="active">Find a Bus</a>
            <a href="tickets.php">My Tickets</a>
        </div>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <h3>My Digital Tickets</h3>

        <?php if (count($tickets) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Route</th>
                    <th>Travel Date</th>
                    <th>Departure</th>
                    <th>Seat No.</th>
                    <th>Ticket No.</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($t['origin'])?> to
                        <?= htmlspecialchars($t['destination'])?>
                    </td>
                    <td>
                        <?= htmlspecialchars($t['travel_date'])?>
                    </td>
                    <td>
                        <?= substr($t['departure_time'], 0, 5)?>
                    </td>
                    <td>
                        <?= htmlspecialchars($t['seat_number'])?>
                    </td>
                    <td>
                        <?= htmlspecialchars($t['ticket_number'])?>
                    </td>
                    <td><a href="view_ticket.php?id=<?= $t['ticket_id']?>" class="btn-view">View Ticket</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align: center; color: #777; margin-top: 40px;">You haven't bought any tickets yet.</p>
        <?php endif; ?>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. MULTI-LEVEL JOIN:
 * To show a single ticket, we need information from four different tables: 
 * tickets, bookings, trips, and routes. We use JOIN to pull them all at once.
 * 
 * 2. FILTERING BY USER:
 * We use $user_id from the session to ensure a passenger only sees their own 
 * tickets, not tickets belonging to someone else.
 * 
 * 3. ORDERING BY DATE:
 * We use "ORDER BY tr.travel_date DESC" to show the most recent or upcoming 
 * trips at the very top of the list.
 */
?>