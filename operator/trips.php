<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];

// Step 1: Fetch all trips for this operator using a JOIN
// Gather trip details along with bus and route information using JOINs
$sql = "SELECT t.*, b.bus_name, b.bus_number, r.origin, r.destination 
        FROM trips t
        JOIN buses b ON t.bus_id = b.id
        JOIN routes r ON t.route_id = r.id
        WHERE t.created_by_operator = $operator_id 
        ORDER BY t.travel_date DESC, t.departure_time DESC";

$result = mysqli_query($conn, $sql);
$trips = [];
while ($row = mysqli_fetch_assoc($result)) {
    $trips[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trips - Mobus</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){
            var t = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", t);
        })();
    </script>
</head>

<body>
    <div class="header">
        <h2>Operator Panel</h2>
        <div class="nav-links">
            <a href="dashboard.php">Manage Buses</a>
            <a href="trips.php" class="active">Manage Trips</a>
            <a href="routes.php">My Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <h3>Scheduled Trips</h3>
        
        <a href="add_trip.php" class="btn btn-add">+ Schedule New Trip</a>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-success">
                <?= htmlspecialchars($_GET['msg'])?>
            </div>
        <?php endif; ?>

        <?php if (count($trips) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Bus Assigned</th>
                    <th>Route (From - To)</th>
                    <th>Date & Time</th>
                    <th>Ticket Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                <tr>
                    <td>#<?= $trip['id']?></td>
                    <td>
                        <?= htmlspecialchars($trip['bus_name'])?> 
                        <small>(<?= htmlspecialchars($trip['bus_number'])?>)</small>
                    </td>
                    <td>
                        <?= htmlspecialchars($trip['origin'])?> &rarr; <?= htmlspecialchars($trip['destination'])?>
                    </td>
                    <td>
                        // Display the date and 24-hour time (HH:MM)
                        <?= $trip['travel_date']?> at <?= substr($trip['departure_time'], 0, 5)?>
                    </td>
                    <td>
                        <strong>UGX <?= number_format($trip['price'], 0)?></strong>
                    </td>
                    <td class="actions">
                        <a href="edit_trip.php?id=<?= $trip['id']?>" class="btn btn-edit">Edit</a>
                        <a href="delete_trip.php?id=<?= $trip['id']?>" class="btn btn-delete"
                            onclick="return confirm('Delete this trip? ALL bookings for this trip will also be deleted!');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No trips have been scheduled yet. Click the button above to start.</p>
        <?php endif; ?>
    </div>

    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. DATABASE JOINS:
 * In the SQL query, we use "JOIN" to combine three tables: trips, buses, and routes. 
 * This allows us to display the bus name and the route names instead of just their ID numbers.
 * 
 * 2. ORDERING:
 * We use "ORDER BY" to show the most recent trips at the top of the table.
 * 
 * 3. FORMATTING:
 * number_format($trip['price'], 0) is used to show currency values with commas (e.g., 50,000).
 * substr() is used to cut off the seconds from the departure time (e.g., 14:00:00 becomes 14:00).
 */
?>