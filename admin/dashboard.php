<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background: #343a40;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 20px;
        }

        a.logout {
            color: #ffcccc;
            text-decoration: none;
        }

        a.logout:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - Super Admin</h2>
        <div>
            Welcome,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <a href="/logout.php" class="logout">Logout</a>
        </div>
    </div>
    <div class="content">
        <h3>Admin Dashboard</h3>
        <p>This is the super admin dashboard. Only users with the 'admin' role can see this.</p>
        <!-- Admin functionalities would go here -->
    </div>
</body>

</html>