<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$user_id = $_SESSION['user_id'];

// Fetch user's tickets (bookings with 'paid' status)
$stmt = $pdo->prepare("
    SELECT tkt.id as ticket_id, b.ticket_number, b.seat_number, 
           tr.departure_time, tr.travel_date, r.origin, r.destination, b.status
    FROM tickets tkt
    JOIN bookings b ON tkt.booking_id = b.id
    JOIN trips tr ON b.trip_id = tr.id
    JOIN routes r ON tr.route_id = r.id
    WHERE b.user_id = ?
    ORDER BY tr.travel_date DESC, tr.departure_time DESC
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - Bus Ticket System</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 20px;
            max-width: 900px;
            margin: auto;
        }

        a.logout {
            color: white;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #f1f1f1;
        }

        .btn-view {
            background: #17a2b8;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-view:hover {
            background: #138496;
        }

        .nav-links {
            display: flex;
            gap: 15px;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .nav-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - App</h2>
        <div class="nav-links">
            <a href="app.php" class="nav-link">Search</a>
            <a href="tickets.php" class="nav-link" style="text-decoration: underline;">My Tickets</a>
        </div>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <a href="/logout.php" class="logout">Logout</a>
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
                        <?= htmlspecialchars($t['origin'])?> -
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
                <?php
    endforeach; ?>
            </tbody>
        </table>
        <?php
else: ?>
        <p style="text-align: center; color: #777; margin-top: 40px;">You haven't bought any tickets yet.</p>
        <?php
endif; ?>
    </div>
</body>

</html>