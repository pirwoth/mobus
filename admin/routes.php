<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');

    if (empty($origin) || empty($destination)) {
        $error = "Both Origin and Destination are required.";
    }
    else {
        // Global routes have created_by_operator = NULL
        $checkStmt = $pdo->prepare("SELECT id FROM routes WHERE origin = ? AND destination = ? AND created_by_operator IS NULL");
        $checkStmt->execute([$origin, $destination]);

        if ($checkStmt->fetch()) {
            $error = "This global route already exists.";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO routes (origin, destination, created_by_operator) VALUES (?, ?, NULL)");
            try {
                $stmt->execute([$origin, $destination]);
                header("Location: routes.php?msg=Global+route+added");
                exit;
            }
            catch (PDOException $e) {
                $error = "Error adding global route.";
            }
        }
    }
}

// Fetch all global routes
$stmt = $pdo->query("SELECT * FROM routes WHERE created_by_operator IS NULL ORDER BY origin ASC");
$routes = $stmt->fetchAll();

// Fetch operator-created routes just for visibility
$stmtLocal = $pdo->query("SELECT r.*, u.name as operator_name FROM routes r JOIN users u ON r.created_by_operator = u.id WHERE r.created_by_operator IS NOT NULL ORDER BY r.origin ASC");
$localRoutes = $stmtLocal->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Routes - Super Admin</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Super Admin - Command Center</h2>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="routes.php" class="active">Global Routes</a>
            <a href="create_user.php">Provision Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">

        <div class="panel">
            <h3>Add Global Route</h3>
            <p class="panel-desc">Global routes are available to ALL operators when scheduling trips.</p>

            <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['msg'])): ?>
            <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
            <?php endif; ?>

            <form method="POST" class="inline-form">
                <div class="form-group">
                    <label>Origin</label>
                    <input type="text" name="origin" required placeholder="e.g. Kampala">
                </div>
                <div class="form-group">
                    <label>Destination</label>
                    <input type="text" name="destination" required placeholder="e.g. Jinja">
                </div>
                <button type="submit">Save Global Route</button>
            </form>
        </div>

        <div class="panel">
            <h3>Active Global Routes</h3>
            <?php if (count($routes) > 0): ?>
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
                    <?php foreach ($routes as $route): ?>
                    <tr>
                        <td>
                            <?= $route['id']?>
                        </td>
                        <td>
                            <?= htmlspecialchars($route['origin'])?>
                        </td>
                        <td>
                            <?= htmlspecialchars($route['destination'])?>
                        </td>
                        <td>
                            <a href="delete_route.php?id=<?= $route['id']?>" class="btn-delete"
                                onclick="return confirm('Delete this global route forever?');">Delete</a>
                        </td>
                    </tr>
                    <?php
    endforeach; ?>
                </tbody>
            </table>
            <?php
else: ?>
            <p>No global routes have been defined yet.</p>
            <?php
endif; ?>
        </div>

        <div class="panel">
            <h3>Operator-Created Routes (Read-Only)</h3>
            <p class="panel-desc">These routes were created by individual operators for their own exclusive use.</p>
            <?php if (count($localRoutes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($localRoutes as $lr): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($lr['origin'])?>
                        </td>
                        <td>
                            <?= htmlspecialchars($lr['destination'])?>
                        </td>
                        <td>
                            <span class="badge">
                                <?= htmlspecialchars($lr['operator_name'])?>
                            </span>
                        </td>
                    </tr>
                    <?php
    endforeach; ?>
                </tbody>
            </table>
            <?php
else: ?>
            <p>No operators have created custom routes.</p>
            <?php
endif; ?>
        </div>

    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>