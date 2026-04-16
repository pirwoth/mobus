<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = (int)$_SESSION['user_id'];
$trip_id = (int)($_GET['id'] ?? 0);

$res = mysqli_query($conn, "SELECT * FROM trips WHERE id = $trip_id AND created_by_operator = $operator_id");
$trip = mysqli_fetch_assoc($res);

if (!$trip) {
    header("Location: trips.php?msg=Trip+not+found+or+access+denied");
    exit;
}

$error = '';

// Fetch buses
$resBuses = mysqli_query($conn, "SELECT id, bus_name, bus_number FROM buses WHERE created_by_operator = $operator_id");
$buses = [];
while ($row = mysqli_fetch_assoc($resBuses)) {
    $buses[] = $row;
}

// Fetch routes
$resRoutes = mysqli_query($conn, "SELECT id, origin, destination FROM routes ORDER BY origin ASC");
$routes = [];
while ($row = mysqli_fetch_assoc($resRoutes)) {
    $routes[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = (int)$_POST['bus_id'];
    $route_id = (int)$_POST['route_id'];
    $departure_time = mysqli_real_escape_string($conn, trim($_POST['departure_time']));
    $travel_date = mysqli_real_escape_string($conn, trim($_POST['travel_date']));
    $price = (float)$_POST['price'];

    if (empty($bus_id) || empty($route_id) || empty($departure_time) || empty($travel_date) || $price <= 0) {
        $error = "All fields are required and price must be greater than 0.";
    }
    else {
        // Validation
        $busCheck = mysqli_query($conn, "SELECT id FROM buses WHERE id = $bus_id AND created_by_operator = $operator_id");
        if (mysqli_num_rows($busCheck) == 0) {
            $error = "Invalid bus selection.";
        }
        else {
            $sql = "UPDATE trips SET bus_id = $bus_id, route_id = $route_id, departure_time = '$departure_time', 
                           travel_date = '$travel_date', price = $price 
                    WHERE id = $trip_id AND created_by_operator = $operator_id";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: trips.php?msg=Trip+updated+successfully");
                exit;
            }
            else {
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
    <title>Edit Trip - Mobus</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System &mdash; Operator</h2>
        <div class="nav-links">
            <a href="dashboard.php">Manage Buses</a>
            <a href="trips.php" class="active">Manage Trips</a>
            <a href="routes.php">Manage Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name']) ?></a>
        </div>
    </div>

    <div class="content">
        <a href="trips.php" class="back-link">&larr; Back to Trips List</a>
        <div class="form-page-grid">
            <div class="panel">
                <h3>Edit Trip</h3>
                <p class="form-desc">Update the details for Trip #<?= $trip_id ?>.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Select Bus</label>
                        <select name="bus_id" required>
                            <?php foreach ($buses as $bus): ?>
                                <option value="<?= $bus['id'] ?>" <?= $bus['id'] == $trip['bus_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($bus['bus_name']) ?> (<?= htmlspecialchars($bus['bus_number']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Route</label>
                        <select name="route_id" required>
                            <?php foreach ($routes as $route): ?>
                                <option value="<?= $route['id'] ?>" <?= $route['id'] == $trip['route_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($route['origin']) ?> to <?= htmlspecialchars($route['destination']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Travel Date</label>
                            <input type="date" name="travel_date" value="<?= $trip['travel_date'] ?>" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Departure Time</label>
                            <input type="time" name="departure_time" value="<?= substr($trip['departure_time'], 0, 5) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Ticket Price (UGX)</label>
                        <input type="number" step="1" name="price" min="1" value="<?= (int)$trip['price'] ?>" required>
                    </div>
                    <button type="submit" class="btn-form-submit">Update Trip</button>
                </form>
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
 * 1. UPDATING TRIPS:
 * This page allows the operator to change the bus, route, date, or time for a 
 * scheduled trip using an SQL UPDATE query.
 * 
 * 2. DROPDOWN SELECTION:
 * We use a ternary operator `<?= $bus['id'] == $trip['bus_id'] ? 'selected' : '' ?>` 
 * to make sure the dropdown matches the trip's current settings when the page loads.
 * 
 * 3. BUSINESS LOGIC:
 * Even when editing, we still check that the `bus_id` provided belongs to the operator. 
 * This prevents cross-tenant data manipulation.
 * 
 * 4. TIME FORMATTING:
 * Databases often store time as 'HH:MM:SS'. We use `substr($trip['departure_time'], 0, 5)` 
 * to strip the seconds so it fits correctly into a standard HTML `<input type="time">`.
 */
?>