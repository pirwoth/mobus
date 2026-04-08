<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

// 1. Fetch Total Users
$stmtUsers = $pdo->query("SELECT COUNT(*) as count FROM users");
$totalUsers = $stmtUsers->fetch()['count'];

// 2. Fetch Active Buses
$stmtBuses = $pdo->query("SELECT COUNT(*) as count FROM buses");
$totalBuses = $stmtBuses->fetch()['count'];

// 3. Fetch Total Trips
$stmtTrips = $pdo->query("SELECT COUNT(*) as count FROM trips");
$totalTrips = $stmtTrips->fetch()['count'];

// 4. Fetch Total Revenue (Only from paid bookings)
$stmtRev = $pdo->query("SELECT SUM(amount) as total_revenue FROM payments WHERE payment_status = 'completed' OR payment_status = 'paid'");
$totalRevenue = $stmtRev->fetch()['total_revenue'] ?? 0;

// Fetch ALL users for management table
$stmtAllUsers = $pdo->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
$allUsers = $stmtAllUsers->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Bus Ticket System</title>
    
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
            <a href="dashboard.php" class="active">Overview</a>
            <a href="routes.php">Global Routes</a>
            <a href="create_user.php">Provision Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">
        <!-- High-Level System Metrics -->
        <div class="stats-grid">
            <div class="stat-card" style="border-top-color: #17a2b8;">
                <h3>Total Users</h3>
                <div class="value">
                    <?= number_format($totalUsers)?>
                </div>
            </div>
            <div class="stat-card" style="border-top-color: #28a745;">
                <h3>Total Revenue</h3>
                <div class="value">UGX
                    <?= number_format($totalRevenue)?>
                </div>
            </div>
            <div class="stat-card" style="border-top-color: #ffc107;">
                <h3>Active Buses</h3>
                <div class="value">
                    <?= number_format($totalBuses)?>
                </div>
            </div>
            <div class="stat-card" style="border-top-color: #dc3545;">
                <h3>Total Trips</h3>
                <div class="value">
                    <?= number_format($totalTrips)?>
                </div>
            </div>
        </div>

        <!-- System Users Table -->
        <div class="panel">
            <h3>Registered Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUsers as $user): ?>
                    <tr>
                        <td>
                            <?= $user['id']?>
                        </td>
                        <td>
                            <?= htmlspecialchars($user['name'])?>
                        </td>
                        <td>
                            <?= htmlspecialchars($user['email'])?>
                        </td>
                        <td>
                            <?= htmlspecialchars($user['phone'])?>
                        </td>
                        <td>
                            <span class="role-badge role-<?= htmlspecialchars($user['role'])?>">
                                <?= strtoupper(htmlspecialchars($user['role']))?>
                            </span>
                        </td>
                        <td>
                            <?= date('M d, Y', strtotime($user['created_at']))?>
                        </td>
                    </tr>
                    <?php
endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>