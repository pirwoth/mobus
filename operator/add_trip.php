<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$error = '';

// Fetch operator's buses
$stmt = $pdo->prepare("SELECT id, bus_name, bus_number FROM buses WHERE created_by_operator = ?");
$stmt->execute([$operator_id]);
$buses = $stmt->fetchAll();

// Fetch all routes
$routes_stmt = $pdo->query("SELECT id, origin, destination FROM routes ORDER BY origin ASC");
$routes = $routes_stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = (int)$_POST['bus_id'];
    $route_id = (int)$_POST['route_id'];
    $departure_time = trim($_POST['departure_time']);
    $travel_date = trim($_POST['travel_date']);
    $price = (float)$_POST['price'];

    if (empty($bus_id) || empty($route_id) || empty($departure_time) || empty($travel_date) || $price <= 0) {
        $error = "All fields are required and price must be greater than 0.";
    }
    else {
        // Verify the bus exists and belongs to this operator to prevent assigning a trip to someone else's bus
        $busCheck = $pdo->prepare("SELECT id FROM buses WHERE id = ? AND created_by_operator = ?");
        $busCheck->execute([$bus_id, $operator_id]);
        if (!$busCheck->fetch()) {
            $error = "Invalid bus selection.";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO trips (bus_id, route_id, departure_time, travel_date, price, created_by_operator) VALUES (?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$bus_id, $route_id, $departure_time, $travel_date, $price, $operator_id]);
                header("Location: trips.php?msg=Trip+scheduled+successfully");
                exit;
            }
            catch (PDOException $e) {
                $error = "Error scheduling trip.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Trip</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            padding-top: 50px;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="trips.php" class="back-link">&larr; Back to Trips List</a>
        <h2>Schedule New Trip</h2>

        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <?php if (count($buses) === 0): ?>
        <div class="error">You must add a bus first before scheduling a trip.</div>
        <a href="add_bus.php" class="back-link">Go add a bus</a>
        <?php
elseif (count($routes) === 0): ?>
        <div class="error">No routes are defined in the system. Please contact the administrator to define routes in the
            database.</div>
        <?php
else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Select Bus</label>
                <select name="bus_id" required>
                    <option value="">-- Choose a bus --</option>
                    <?php foreach ($buses as $bus): ?>
                    <option value="<?= $bus['id']?>">
                        <?= htmlspecialchars($bus['bus_name'])?> (
                        <?= htmlspecialchars($bus['bus_number'])?>)
                    </option>
                    <?php
    endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Route</label>
                <select name="route_id" required>
                    <option value="">-- Choose a route --</option>
                    <?php foreach ($routes as $route): ?>
                    <option value="<?= $route['id']?>">
                        <?= htmlspecialchars($route['origin'])?> to
                        <?= htmlspecialchars($route['destination'])?>
                    </option>
                    <?php
    endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Travel Date</label>
                <input type="date" name="travel_date" required>
            </div>
            <div class="form-group">
                <label>Departure Time</label>
                <input type="time" name="departure_time" required>
            </div>
            <div class="form-group">
                <label>Ticket Price</label>
                <input type="number" step="0.01" name="price" min="1" required>
            </div>
            <button type="submit">Schedule Trip</button>
        </form>
        <?php
endif; ?>
    </div>
</body>

</html>