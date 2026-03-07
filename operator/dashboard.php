<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];

// Fetch buses for this operator
$stmt = $pdo->prepare("SELECT * FROM buses WHERE created_by_operator = ? ORDER BY created_at DESC");
$stmt->execute([$operator_id]);
$buses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Dashboard - Manage Buses</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
        }

        .header {
            background: #17a2b8;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            background: white;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        a.logout {
            color: white;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #dee2e6;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 14px;
        }

        .btn-add {
            background: #28a745;
            margin-bottom: 15px;
        }

        .btn-edit {
            background: #ffc107;
            color: black;
        }

        .btn-delete {
            background: #dc3545;
        }

        .actions {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - Operator</h2>
        <div class="nav-links" style="display: flex; gap: 15px; align-items: center;">
            <a href="dashboard.php" style="color: white; font-weight: bold; text-decoration: underline;">Manage
                Buses</a>
            <a href="trips.php" style="color: white; text-decoration: none;">Manage Trips</a>
            <a href="/logout.php" style="color: white; text-decoration: none;">Logout (
                <?= htmlspecialchars($_SESSION['name'])?>)
            </a>
        </div>
    </div>
    <div class="content">
        <h3>Manage Buses</h3>
        <a href="add_bus.php" class="btn btn-add">+ Add New Bus</a>

        <?php if (isset($_GET['msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
            <?= htmlspecialchars($_GET['msg'])?>
        </div>
        <?php
endif; ?>

        <?php if (count($buses) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bus Name</th>
                    <th>Bus Number</th>
                    <th>Total Seats</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                <tr>
                    <td>
                        <?= $bus['id']?>
                    </td>
                    <td>
                        <?= htmlspecialchars($bus['bus_name'])?>
                    </td>
                    <td>
                        <?= htmlspecialchars($bus['bus_number'])?>
                    </td>
                    <td>
                        <?= $bus['total_seats']?>
                    </td>
                    <td>
                        <?= $bus['created_at']?>
                    </td>
                    <td class="actions">
                        <a href="edit_bus.php?id=<?= $bus['id']?>" class="btn btn-edit">Edit</a>
                        <a href="delete_bus.php?id=<?= $bus['id']?>" class="btn btn-delete"
                            onclick="return confirm('Are you sure you want to delete this bus?');">Delete</a>
                    </td>
                </tr>
                <?php
    endforeach; ?>
            </tbody>
        </table>
        <?php
else: ?>
        <p>No buses found. Please add a bus.</p>
        <?php
endif; ?>
    </div>
</body>

</html>