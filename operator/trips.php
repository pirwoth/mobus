<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];

// Fetch trips for this operator
$stmt = $pdo->prepare("
    SELECT t.*, b.bus_name, b.bus_number, r.origin, r.destination 
    FROM trips t
    JOIN buses b ON t.bus_id = b.id
    JOIN routes r ON t.route_id = r.id
    WHERE t.created_by_operator = ? 
    ORDER BY t.travel_date DESC, t.departure_time DESC
");
$stmt->execute([$operator_id]);
$trips = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Manage Trips</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - Operator</h2>
        <div class="nav-links">
            <a href="dashboard.php" >Manage Buses</a>
            <a href="trips.php" class="active">Manage Trips</a>
            <a href="routes.php" >Manage Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>
    <div class="content">
        <h3>Manage Trips</h3>
        <a href="add_trip.php" class="btn btn-add">+ Schedule New Trip</a>

        <?php if (isset($_GET['msg'])): ?>
        <div class="msg-success">
            <?= htmlspecialchars($_GET['msg'])?>
        </div>
        <?php
endif; ?>

        <?php if (count($trips) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Bus</th>
                    <th>Route</th>
                    <th>Travel Date</th>
                    <th>Departure</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $trip): ?>
                <tr>
                    <td>
                        <?= $trip['id']?>
                    </td>
                    <td>
                        <?= htmlspecialchars($trip['bus_name'])?> (
                        <?= htmlspecialchars($trip['bus_number'])?>)
                    </td>
                    <td>
                        <?= htmlspecialchars($trip['origin'])?> to
                        <?= htmlspecialchars($trip['destination'])?>
                    </td>
                    <td>
                        <?= $trip['travel_date']?>
                    </td>
                    <td>
                        <?= $trip['departure_time']?>
                    </td>
                    <td>$
                        <?= number_format($trip['price'], 2)?>
                    </td>
                    <td class="actions">
                        <a href="edit_trip.php?id=<?= $trip['id']?>" class="btn btn-edit">Edit</a>
                        <a href="delete_trip.php?id=<?= $trip['id']?>" class="btn btn-delete"
                            onclick="return confirm('Are you sure you want to delete this trip?');">Delete</a>
                    </td>
                </tr>
                <?php
    endforeach; ?>
            </tbody>
        </table>
        <?php
else: ?>
        <p>No trips scheduled. Please schedule a trip.</p>
        <?php
endif; ?>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>