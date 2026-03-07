<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$trip_id = $_GET['id'] ?? 0;

// Fetch trip to edit, ensuring it belongs to this operator
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ? AND created_by_operator = ?");
$stmt->execute([$trip_id, $operator_id]);
$trip = $stmt->fetch();

if (!$trip) {
    header("Location: trips.php?msg=Trip+not+found+or+access+denied");
    exit;
}

$error = '';

// Fetch operator's buses
$busesStmt = $pdo->prepare("SELECT id, bus_name, bus_number FROM buses WHERE created_by_operator = ?");
$busesStmt->execute([$operator_id]);
$buses = $busesStmt->fetchAll();

// Fetch all routes
$routesStmt = $pdo->query("SELECT id, origin, destination FROM routes ORDER BY origin ASC");
$routes = $routesStmt->fetchAll();

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
        // Verify the bus exists and belongs to this operator
        $busCheck = $pdo->prepare("SELECT id FROM buses WHERE id = ? AND created_by_operator = ?");
        $busCheck->execute([$bus_id, $operator_id]);
        if (!$busCheck->fetch()) {
            $error = "Invalid bus selection.";
        }
        else {
            $updateStmt = $pdo->prepare("UPDATE trips SET bus_id = ?, route_id = ?, departure_time = ?, travel_date = ?, price = ? WHERE id = ? AND created_by_operator = ?");
            try {
                $updateStmt->execute([$bus_id, $route_id, $departure_time, $travel_date, $price, $trip_id, $operator_id]);
                header("Location: trips.php?msg=Trip+updated+successfully");
                exit;
            }
            catch (PDOException $e) {
                $error = "Error updating trip.";
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
    <title>Edit Trip</title>
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
            background: #ffc107;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background: #e0a800;
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
        <h2>Edit Trip</h2>

        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Select Bus</label>
                <select name="bus_id" required>
                    <?php foreach ($buses as $bus): ?>
                    <option value="<?= $bus['id']?>" <?=$bus['id']==$trip['bus_id'] ? 'selected' : ''?>>
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
                    <?php foreach ($routes as $route): ?>
                    <option value="<?= $route['id']?>" <?=$route['id']==$trip['route_id'] ? 'selected' : ''?>>
                        <?= htmlspecialchars($route['origin'])?> to
                        <?= htmlspecialchars($route['destination'])?>
                    </option>
                    <?php
endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Travel Date</label>
                <input type="date" name="travel_date" value="<?= $trip['travel_date']?>" required>
            </div>
            <div class="form-group">
                <label>Departure Time</label>
                <input type="time" name="departure_time" value="<?= substr($trip['departure_time'], 0, 5)?>" required>
            </div>
            <div class="form-group">
                <label>Ticket Price</label>
                <input type="number" step="0.01" name="price" min="1" value="<?= $trip['price']?>" required>
            </div>
            <button type="submit">Update Trip</button>
        </form>
    </div>
</body>

</html>