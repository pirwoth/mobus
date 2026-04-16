<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_name = mysqli_real_escape_string($conn, trim($_POST['bus_name']));
    $bus_number = mysqli_real_escape_string($conn, trim($_POST['bus_number']));
    $total_seats = (int)$_POST['total_seats'];
    $operator_id = $_SESSION['user_id'];

    if (empty($bus_name) || empty($bus_number) || $total_seats <= 0) {
        $error = "All fields are required and total seats must be greater than 0.";
    }
    else {
        $sql = "INSERT INTO buses (bus_name, bus_number, total_seats, created_by_operator) 
                VALUES ('$bus_name', '$bus_number', $total_seats, $operator_id)";
        
        if (mysqli_query($conn, $sql)) {
            $bus_id = mysqli_insert_id($conn);

            for ($i = 1; $i <= $total_seats; $i++) {
                mysqli_query($conn, "INSERT INTO seats (bus_id, seat_number) VALUES ($bus_id, $i)");
            }

            header("Location: dashboard.php?msg=Bus+added+successfully");
            exit;
        }
        else {
            $error = "Error adding bus. Bus number might already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bus - Mobus</title>
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
            <a href="dashboard.php" class="active">Manage Buses</a>
            <a href="trips.php">Manage Trips</a>
            <a href="routes.php">Manage Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name']) ?></a>
        </div>
    </div>

    <div class="content">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <div class="form-page-grid">
            <div class="panel">
                <h3>Add New Bus</h3>
                <p class="form-desc">Add a bus to your fleet. Seats will be automatically generated.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Bus Name</label>
                        <input type="text" name="bus_name" placeholder="e.g. YY Coach" required>
                    </div>
                    <div class="form-group">
                        <label>Bus Number (Plate)</label>
                        <input type="text" name="bus_number" placeholder="e.g. KCA 123A" required>
                    </div>
                    <div class="form-group">
                        <label>Total Seats</label>
                        <input type="number" name="total_seats" min="1" max="100" placeholder="e.g. 45" required>
                    </div>
                    <button type="submit" class="btn-form-submit">Save Bus</button>
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
 * 1. ADDING A BUS:
 * When a bus is added, we save its name, plate number, and capacity in the 'buses' table.
 * 
 * 2. AUTO-GENERATING SEATS:
 * We use a "FOR loop" to create individual seat records in the 'seats' table. 
 * If a bus has 45 seats, the loop runs 45 times to insert seats 1 through 45.
 * 
 * 3. RETRIEVING THE NEW ID:
 * We use mysqli_insert_id($conn) to get the ID of the bus we just saved. 
 * This is necessary so we can link the new seats to the correct bus.
 * 
 * 4. MULTI-OPERATOR SYSTEM:
 * We save the `operator_id` (from the session) with the bus record. 
 * This ensures that only the operator who added the bus can manage it.
 */
?>