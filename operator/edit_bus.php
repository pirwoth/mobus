<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = (int)$_SESSION['user_id'];
$bus_id = (int)($_GET['id'] ?? 0);

// Verify that the requested bus ID exists and belongs to the current operator
$res = mysqli_query($conn, "SELECT * FROM buses WHERE id = $bus_id AND created_by_operator = $operator_id");
$bus = mysqli_fetch_assoc($res);

if (!$bus) {
    header("Location: dashboard.php?msg=Bus+not+found+or+access+denied");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_name = mysqli_real_escape_string($conn, trim($_POST['bus_name']));
    $bus_number = mysqli_real_escape_string($conn, trim($_POST['bus_number']));
    $total_seats = (int)$_POST['total_seats'];

    if (empty($bus_name) || empty($bus_number) || $total_seats <= 0) {
        $error = "All fields are required and total seats must be greater than 0.";
    }
    else {
        // Update bus details while strictly enforcing ownership via WHERE clause
        $sql = "UPDATE buses SET bus_name = '$bus_name', bus_number = '$bus_number', total_seats = $total_seats 
                WHERE id = $bus_id AND created_by_operator = $operator_id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: dashboard.php?msg=Bus+updated+successfully");
            exit;
        }
        else {
            $error = "Error updating bus.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bus - Mobus</title>
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
                <h3>Edit Bus</h3>
                <p class="form-desc">Update the details of <strong><?= htmlspecialchars($bus['bus_name']) ?></strong>.</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Bus Name</label>
                        <input type="text" name="bus_name" value="<?= htmlspecialchars($bus['bus_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Bus Number (Plate)</label>
                        <input type="text" name="bus_number" value="<?= htmlspecialchars($bus['bus_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Total Seats</label>
                        <input type="number" name="total_seats" min="1" max="100" value="<?= $bus['total_seats'] ?>" required>
                    </div>
                    <button type="submit" class="btn-form-submit">Update Bus</button>
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
 * 1. EDITING DATA:
 * We use the SQL UPDATE command to change existing information in the 'buses' table.
 * 
 * 2. OWNER VERIFICATION:
 * Before showing the form, we query the database to make sure the bus actually 
 * belongs to the logged-in operator. If not, we redirect them back to the dashboard.
 * 
 * 3. PRE-FILLING THE FORM:
 * We want the operator to see the current bus details before they change anything. 
 * We do this by setting the `value` attribute of each input box to the current 
 * database values.
 * 
 * 4. VALIDATION:
 * We check if the bus number being saved is already taken by another bus. This 
 * prevents duplicate plate numbers in our system.
 */
?>