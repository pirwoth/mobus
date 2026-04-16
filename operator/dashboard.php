<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];

// Step 1: Fetch buses for this operator
$sql = "SELECT * FROM buses WHERE created_by_operator = $operator_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$buses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Mobus</title>
    
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
        <h2>Operator Panel</h2>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Manage Buses</a>
            <a href="trips.php">Manage Trips</a>
            <a href="routes.php">My Routes</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout (<?= htmlspecialchars($_SESSION['name'])?>)</a>
        </div>
    </div>

    <div class="content">
        <h3>My Fleet (Buses)</h3>
        
        <a href="add_bus.php" class="btn btn-add">+ Register New Bus</a>

        <?php if (isset($_GET['msg'])): ?>
            <div class="msg-success">
                <?= htmlspecialchars($_GET['msg'])?>
            </div>
        <?php endif; ?>

        <?php if (count($buses) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bus Name</th>
                    <th>Plate Number</th>
                    <th>Capacity (Seats)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?= $bus['id']?></td>
                    <td><?= htmlspecialchars($bus['bus_name'])?></td>
                    <td><?= htmlspecialchars($bus['bus_number'])?></td>
                    <td><?= $bus['total_seats']?> seats</td>
                    <td class="actions">
                        <a href="edit_bus.php?id=<?= $bus['id']?>" class="btn btn-edit">Edit</a>
                        <a href="delete_bus.php?id=<?= $bus['id']?>" class="btn btn-delete"
                            onclick="return confirm('Deleting this bus will also remove its trips. Continue?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>You haven't added any buses yet. Click the button above to add one.</p>
        <?php endif; ?>
    </div>

    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. FILTERING DATA:
 * We use the $operator_id from the session to make sure an operator only sees 
 * their own buses, not buses belonging to another company.
 * 
 * 2. MYSQLI QUERY:
 * mysqli_query($conn, $sql) sends our request to the database. If successful, 
 * it returns a "result object" which contains the rows.
 * 
 * 3. THE WHILE LOOP:
 * Since there might be many buses, we use while($row = mysqli_fetch_assoc($result)) 
 * to go through each bus one by one and add it to our $buses list.
 * 
 * 4. SECURITY NOTE:
 * Even though $operator_id comes from a session (safe), it is good practice 
 * to ensure all variables used in SQL are handled carefully.
 */
?>