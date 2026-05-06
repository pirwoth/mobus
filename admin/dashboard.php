<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

// --- 1. Fetch Summary Data (Calculate system-wide statistics) ---
$resUsers = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$totalUsers = mysqli_fetch_assoc($resUsers)['count'];

$resBuses = mysqli_query($conn, "SELECT COUNT(*) as count FROM buses");
$totalBuses = mysqli_fetch_assoc($resBuses)['count'];

$resTrips = mysqli_query($conn, "SELECT COUNT(*) as count FROM trips");
$totalTrips = mysqli_fetch_assoc($resTrips)['count'];

// Aggregate total revenue from all successful payments in the system
$resRev = mysqli_query($conn, "SELECT SUM(amount) as total_revenue FROM payments WHERE payment_status = 'completed' OR payment_status = 'paid'");
$revData = mysqli_fetch_assoc($resRev);
$totalRevenue = $revData['total_revenue'] ?? 0;

// --- 2. Handle Filtering ---
$roleFilter = $_GET['role'] ?? '';
$whereClause = "";
if (!empty($roleFilter)) {
    $safeRole = mysqli_real_escape_string($conn, $roleFilter);
    // Dynamically build SQL WHERE clause for role-based filtering
    $whereClause = "WHERE role = '$safeRole'";
}

// --- 3. Fetch User List ---
$sqlAll = "SELECT id, name, email, phone, role, created_at FROM users $whereClause ORDER BY created_at DESC";
$resAll = mysqli_query($conn, $sqlAll);
$allUsers = [];
while ($row = mysqli_fetch_assoc($resAll)) {
    $allUsers[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mobus</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function () {
            var t = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", t);
        })();
    </script>
</head>

<body>
    <div class="header">
        <h2>Super Admin Panel</h2>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Overview</a>
            <a href="routes.php">Global Routes</a>
            <a href="create_user.php">Manage Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout
                (<?= htmlspecialchars($_SESSION['name']) ?>)</a>
        </div>
    </div>

    <div class="content">
        <div class="stats-grid">
            <div class="stat-card" style="border-top-color: #17a2b8;">
                <h3>Total Users</h3>
                <div class="value"><?= number_format($totalUsers) ?></div>
            </div>

            <div class="stat-card" style="border-top-color: #28a745;">
                <h3>Total Revenue</h3>
                <div class="value">UGX <?= number_format($totalRevenue) ?></div>
            </div>

            <div class="stat-card" style="border-top-color: #ffc107;">
                <h3>Buses</h3>
                <div class="value"><?= number_format($totalBuses) ?></div>
            </div>

            <div class="stat-card" style="border-top-color: #dc3545;">
                <h3>Trips</h3>
                <div class="value"><?= number_format($totalTrips) ?></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <h3>System Users</h3>
                <form method="GET" class="filter-form">
                    <!-- Auto-submit the filter form when the user selects a role -->
                    <select name="role" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="operator" <?= $roleFilter === 'operator' ? 'selected' : '' ?>>Operator</option>
                        <option value="verifier" <?= $roleFilter === 'verifier' ? 'selected' : '' ?>>Verifier</option>
                        <option value="passenger" <?= $roleFilter === 'passenger' ? 'selected' : '' ?>>Passenger</option>
                    </select>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email / Phone</th>
                        <th>Role</th>
                        <th>Joined On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($allUsers) > 0): ?>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><strong><?= htmlspecialchars($user['name']) ?></strong></td>
                                <td>
                                    <?= htmlspecialchars($user['email']) ?><br>
                                    <small style="color: #888;"><?= htmlspecialchars($user['phone']) ?></small>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center;">No users found for this role.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. STATS FETCHING:
 * We run multiple mysqli_query() calls to count users, buses, and trips. 
 * mysqli_fetch_assoc() is then used to get the numeric count from the column.
 * 
 * 2. AGGREGATES:
 * For the revenue, we use SUM(amount). This adds up all the values in the amount column 
 * for confirmed payments.
 * 
 * 3. USERS LOOP:
 * To list all users, we use a while loop with mysqli_fetch_assoc($resAll). 
 * This keeps grabbing rows one by one until there are no more left in the database result.
 */
?>