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
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
        }

        .header {
            background: #17a2b8;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .content {
            padding: 20px;
            max-width: 1100px;
            margin: auto;
            background: white;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #dee2e6;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 14px;
        }

        .btn-add {
            background: #28a745;
            margin-bottom: 15px;
        }

        .btn-edit {
            background: #ffc107;
            color: black;
        }

        .btn-delete {
            background: #dc3545;
        }

        .actions {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - Operator</h2>
        <div class="nav-links">
            <a href="dashboard.php">Manage Buses</a>
            <a href="trips.php" style="font-weight: bold; text-decoration: underline;">Manage Trips</a>
            <a href="/logout.php">Logout (
                <?= htmlspecialchars($_SESSION['name'])?>)
            </a>
        </div>
    </div>
    <div class="content">
        <h3>Manage Trips</h3>
        <a href="add_trip.php" class="btn btn-add">+ Schedule New Trip</a>

        <?php if (isset($_GET['msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
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
</body>

</html>