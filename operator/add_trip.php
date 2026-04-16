<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$error = '';

// Fetch operator's buses
$resBuses = mysqli_query($conn, "SELECT id, bus_name, bus_number FROM buses WHERE created_by_operator = $operator_id");
$buses = [];
while ($row = mysqli_fetch_assoc($resBuses)) {
    $buses[] = $row;
}

// Fetch global and operator routes
$resRoutes = mysqli_query($conn, "SELECT id, origin, destination FROM routes WHERE created_by_operator = $operator_id OR created_by_operator IS NULL ORDER BY origin ASC");
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
        // Validation check
        $busCheck = mysqli_query($conn, "SELECT id FROM buses WHERE id = $bus_id AND created_by_operator = $operator_id");
        if (mysqli_num_rows($busCheck) == 0) {
            $error = "Invalid bus selection.";
        }
        else {
            $sql = "INSERT INTO trips (bus_id, route_id, departure_time, travel_date, price, created_by_operator) 
                    VALUES ($bus_id, $route_id, '$departure_time', '$travel_date', $price, $operator_id)";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: trips.php?msg=Trip+scheduled+successfully");
                exit;
            }
            else {
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
    <title>Schedule Trip - Mobus</title>
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
                <h3>Schedule New Trip</h3>
                <p class="form-desc">Assign a bus to a route with a travel date, departure time, and ticket price.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (count($buses) === 0): ?>
                    <div class="error">You must add a bus first before scheduling a trip.</div>
                    <a href="add_bus.php" class="back-link">Go add a bus &rarr;</a>
                <?php elseif (count($routes) === 0): ?>
                    <div class="error">No routes are defined. Please contact the administrator to define routes.</div>
                <?php else: ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>Select Bus</label>
                            <select name="bus_id" required>
                                <option value="">-- Choose a bus --</option>
                                <?php foreach ($buses as $bus): ?>
                                    <option value="<?= $bus['id'] ?>"><?= htmlspecialchars($bus['bus_name']) ?> (<?= htmlspecialchars($bus['bus_number']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Route</label>
                            <select name="route_id" required>
                                <option value="">-- Choose a route --</option>
                                <?php foreach ($routes as $route): ?>
                                    <option value="<?= $route['id'] ?>"><?= htmlspecialchars($route['origin']) ?> to <?= htmlspecialchars($route['destination']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Travel Date</label>
                                <input type="date" name="travel_date" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Departure Time</label>
                                <input type="time" name="departure_time" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Ticket Price (UGX)</label>
                            <input type="number" step="1" name="price" min="1" placeholder="e.g. 25000" required>
                        </div>
                        <button type="submit" class="btn-form-submit">Schedule Trip</button>
                    </form>
                <?php endif; ?>
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
 * 1. DYNAMIC DROPDOWNS:
 * We fetch all available buses and routes from the database. This ensures the operator 
 * can only pick from valid options that already exist in the system.
 * 
 * 2. DATA CROSS-CHECK:
 * When the form is submitted, we verify that the selected `bus_id` actually belongs 
 * to the logged-in operator. This prevents users from trying to assign another 
 * operator's bus to their trip via manual form manipulation.
 * 
 * 3. DATE RESTRICTION:
 * We use the `min` attribute on the date input so that operators cannot schedule 
 * a trip for a date that has already passed.
 * 
 * 4. ROUTE ACCESSIBILITY:
 * Operators can see "Global Routes" (created by Admins) and their own "Local Routes" 
 * (created by them).
 */
?>