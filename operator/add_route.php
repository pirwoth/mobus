<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = mysqli_real_escape_string($conn, trim($_POST['origin'] ?? ''));
    $destination = mysqli_real_escape_string($conn, trim($_POST['destination'] ?? ''));

    if (empty($origin) || empty($destination)) {
        $error = "Both Origin and Destination are required.";
    }
    else {
        // Uniqueness check
        $sqlCheck = "SELECT id FROM routes 
                     WHERE origin = '$origin' AND destination = '$destination' 
                     AND (created_by_operator = $operator_id OR created_by_operator IS NULL)";
        $resCheck = mysqli_query($conn, $sqlCheck);

        if (mysqli_num_rows($resCheck) > 0) {
            $error = "This route already exists in your list or globally.";
        }
        else {
            $sql = "INSERT INTO routes (origin, destination, created_by_operator) 
                    VALUES ('$origin', '$destination', $operator_id)";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: routes.php?msg=Route+added+successfully");
                exit;
            }
            else {
                $error = "Error adding route.";
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
    <title>Add Route - Mobus</title>
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
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. LOCAL ROUTES:
 * Operators can add their own "Local Routes" that only they can use. This is 
 * useful if an operator wants to create a specific specialized route that 
 * isn't globally defined.
 * 
 * 2. DUPLICATION CHECK:
 * The system checks if the route already exists to prevent duplicates. It checks 
 * both the Global list (NULL operator) and the current operator's own list.
 * 
 * 3. SQL SANITIZATION:
 * We use mysqli_real_escape_string() to clean the 'origin' and 'destination' 
 * inputs. This prevents hackers from trying to run SQL commands through 
 * the text boxes.
 */
?>