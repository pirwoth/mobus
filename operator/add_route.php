<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');

    if (empty($origin) || empty($destination)) {
        $error = "Both Origin and Destination are required.";
    }
    else {
        $checkStmt = $pdo->prepare("SELECT id FROM routes WHERE origin = ? AND destination = ? AND (created_by_operator = ? OR created_by_operator IS NULL)");
        $checkStmt->execute([$origin, $destination, $operator_id]);

        if ($checkStmt->fetch()) {
            $error = "This route already exists in your list or globally.";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO routes (origin, destination, created_by_operator) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$origin, $destination, $operator_id]);
                header("Location: routes.php?msg=Route+added+successfully");
                exit;
            }
            catch (PDOException $e) {
                $error = "Error adding route: " . $e->getMessage();
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
    <title>Add Route - Operator</title>
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
            <a href="dashboard.php">Manage Buses</a>
            <a href="trips.php">Manage Trips</a>
            <a href="routes.php" class="active">Manage Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name']) ?></a>
        </div>
    </div>

    <div class="content">
        <a href="routes.php" class="back-link">&larr; Back to Routes List</a>
        <div class="form-page-grid">
            <div class="panel">
                <h3>Add New Route</h3>
                <p class="form-desc">Define a new route by specifying the origin and destination cities or stations.</p>

                <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Origin (City/Station)</label>
                        <input type="text" name="origin" required placeholder="e.g. Kampala">
                    </div>
                    <div class="form-group">
                        <label>Destination (City/Station)</label>
                        <input type="text" name="destination" required placeholder="e.g. Jinja">
                    </div>
                    <button type="submit" class="btn-form-submit">Save Route</button>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>