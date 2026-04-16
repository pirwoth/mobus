<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$error = '';

/**
 * Handle Adding a Global Route
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = mysqli_real_escape_string($conn, $_POST['origin'] ?? '');
    $destination = mysqli_real_escape_string($conn, $_POST['destination'] ?? '');

    if (empty($origin) || empty($destination)) {
        $error = "Both Origin and Destination are required.";
    }
    else {
        // Step 1: Check if this Global route already exists
        $checkSql = "SELECT id FROM routes WHERE origin = '$origin' AND destination = '$destination' AND created_by_operator IS NULL";
        $checkRes = mysqli_query($conn, $checkSql);

        if (mysqli_num_rows($checkRes) > 0) {
            $error = "This global route already exists in the system.";
        }
        else {
            // Step 2: Insert the new route
            $sql = "INSERT INTO routes (origin, destination, created_by_operator) VALUES ('$origin', '$destination', NULL)";
            if (mysqli_query($conn, $sql)) {
                header("Location: routes.php?msg=Global route added successfully");
                exit;
            }
            else {
                $error = "An error occurred while adding the route.";
            }
        }
    }
}

// --- 3. Fetch Data for Display ---

// Fetch all Global routes
$resGlobal = mysqli_query($conn, "SELECT * FROM routes WHERE created_by_operator IS NULL ORDER BY origin ASC");
$globalRoutes = [];
while ($row = mysqli_fetch_assoc($resGlobal)) {
    $globalRoutes[] = $row;
}

// Fetch local routes (created by Operators)
$sqlLocal = "SELECT r.*, u.name as operator_name 
             FROM routes r 
             JOIN users u ON r.created_by_operator = u.id 
             WHERE r.created_by_operator IS NOT NULL 
             ORDER BY r.origin ASC";
$resLocal = mysqli_query($conn, $sqlLocal);
$localRoutes = [];
while ($row = mysqli_fetch_assoc($resLocal)) {
    $localRoutes[] = $row;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Routes - Admin</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){
            var t = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", t);
        })();
    </script>
</head>

<body>
    <div class="header">
        <h2>Super Admin Panel</h2>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="routes.php" class="active">Global Routes</a>
            <a href="create_user.php">Manage Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout (<?= htmlspecialchars($_SESSION['name'])?>)</a>
        </div>
    </div>

    <div class="content">

        <div class="panel">
            <h3>Add a New Global Route</h3>
            <p>Note: Global routes can be used by any bus operator.</p>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="msg-success"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>

            <form method="POST" class="inline-form">
                <div class="form-group">
                    <label>Starting Point (Origin)</label>
                    <input type="text" name="origin" required placeholder="e.g. Kampala">
                </div>
                <div class="form-group">
                    <label>End Point (Destination)</label>
                    <input type="text" name="destination" required placeholder="e.g. Jinja">
                </div>
                <button type="submit">Add Global Route</button>
            </form>
        </div>

        <div class="panel">
            <h3>Active Global Routes</h3>
            <?php if (count($globalRoutes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($globalRoutes as $route): ?>
                    <tr>
                        <td><?= $route['id']?></td>
                        <td><?= htmlspecialchars($route['origin'])?></td>
                        <td><?= htmlspecialchars($route['destination'])?></td>
                        <td>
                            <a href="delete_route.php?id=<?= $route['id']?>" class="btn-delete"
                                onclick="return confirm('Delete this global route?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No global routes have been added yet.</p>
            <?php endif; ?>
        </div>

        <div class="panel">
            <h3>Operator-Created Routes (Local)</h3>
            <p>Admin cannot edit these; they belong to specific operators.</p>
            <?php if (count($localRoutes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Managing Company/Staff</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($localRoutes as $lr): ?>
                    <tr>
                        <td><?= htmlspecialchars($lr['origin'])?></td>
                        <td><?= htmlspecialchars($lr['destination'])?></td>
                        <td>
                            <span class="badge"><?= htmlspecialchars($lr['operator_name'])?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No operator-specific routes found.</p>
            <?php endif; ?>
        </div>

    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. ADDING DATA:
 * When adding a route, we first use mysqli_real_escape_string() to clean the names.
 * We then check if the same route already exists using mysqli_num_rows().
 * 
 * 2. FETCHING RESULTS:
 * We use a "while loop" with mysqli_fetch_assoc() to turn the database result 
 * into a clean PHP Array. Each row in the array represents one route.
 * 
 * 3. GLOBAL VS LOCAL:
 * Global routes have `created_by_operator` set to NULL. 
 * Local routes are linked to an operator's ID using a JOIN query.
 * 
 * 4. SECURITY:
 * Always clean inputs before putting them in a SQL string to prevent SQL Injection attacks.
 */
?>