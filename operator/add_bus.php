<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_name = trim($_POST['bus_name']);
    $bus_number = trim($_POST['bus_number']);
    $total_seats = (int)$_POST['total_seats'];
    $operator_id = $_SESSION['user_id'];

    if (empty($bus_name) || empty($bus_number) || $total_seats <= 0) {
        $error = "All fields are required and total seats must be greater than 0.";
    }
    else {
        $stmt = $pdo->prepare("INSERT INTO buses (bus_name, bus_number, total_seats, created_by_operator) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$bus_name, $bus_number, $total_seats, $operator_id]);
            $bus_id = $pdo->lastInsertId();

            // Auto-generate seats for this bus
            $seatStmt = $pdo->prepare("INSERT INTO seats (bus_id, seat_number) VALUES (?, ?)");
            for ($i = 1; $i <= $total_seats; $i++) {
                $seatStmt->execute([$bus_id, $i]);
            }

            header("Location: dashboard.php?msg=Bus+added+successfully");
            exit;
        }
        catch (PDOException $e) {
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
    <title>Add Bus - Operator</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
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
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>