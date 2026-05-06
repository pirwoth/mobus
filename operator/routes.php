<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];

// Fetch routes for this operator and global routes
// Fetch routes specific to this operator OR global routes created by Admin
$sql = "SELECT * FROM routes WHERE created_by_operator = $operator_id OR created_by_operator IS NULL ORDER BY origin ASC";
$result = mysqli_query($conn, $sql);
$routes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $routes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Routes - Mobus</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - Operator</h2>
        <div class="nav-links">
            <a href="dashboard.php" >Manage Buses</a>
            <a href="trips.php" >Manage Trips</a>
            <a href="routes.php" class="active">Manage Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">
        <h3>Manage Routes</h3>
        <a href="add_route.php" class="btn btn-add">+ Add New Route</a>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-success">
                <?= htmlspecialchars($_GET['msg'])?>
            </div>
        <?php endif; ?>

        <?php if (count($routes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($routes as $route): ?>
                <tr>
                    <td><?= $route['id']?></td>
                    <td><?= htmlspecialchars($route['origin'])?></td>
                    <td><?= htmlspecialchars($route['destination'])?></td>
                    <td>
                        <?php if ($route['created_by_operator'] === null): ?>
                            <span class="global-badge">Global (Admin)</span>
                        <?php else: ?>
                            Local (Yours)
                        <?php endif; ?>
                    </td>
                    <td>
                        // Only show delete option if the operator created this route themselves
                        <?php if ($route['created_by_operator'] == $operator_id): ?>
                            <a href="delete_route.php?id=<?= $route['id']?>" class="btn btn-delete"
                                onclick="return confirm('Delete this route? Trips using this route will ALSO be deleted!');">Delete</a>
                        <?php else: ?>
                            <span style="color: #999; font-size: 12px;">No Actions</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>No routes found. Please add a route.</p>
        <?php endif; ?>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. ROUTE TYPES:
 * - GLOBAL: Created by an Admin. Every operator can use these routes to schedule trips.
 * - LOCAL: Created by you (the operator). Only you can see and use these.
 * 
 * 2. ROUTE FILTERING:
 * Our SQL query uses "OR created_by_operator IS NULL" to specifically pull both 
 * your own routes and the system-wide routes.
 * 
 * 3. PERMISSIONS:
 * We use an IF statement in the table to hide the 'Delete' button for Global routes. 
 * An operator is only allowed to delete the routes they created themselves.
 */
?>