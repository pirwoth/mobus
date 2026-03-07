<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('operator');

$operator_id = $_SESSION['user_id'];
$bus_id = $_GET['id'] ?? 0;

// Fetch bus to edit, ensuring it belongs to this operator
$stmt = $pdo->prepare("SELECT * FROM buses WHERE id = ? AND created_by_operator = ?");
$stmt->execute([$bus_id, $operator_id]);
$bus = $stmt->fetch();

if (!$bus) {
    header("Location: dashboard.php?msg=Bus+not+found+or+access+denied");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_name = trim($_POST['bus_name']);
    $bus_number = trim($_POST['bus_number']);
    $total_seats = (int)$_POST['total_seats'];

    if (empty($bus_name) || empty($bus_number) || $total_seats <= 0) {
        $error = "All fields are required and total seats must be greater than 0.";
    }
    else {
        $updateStmt = $pdo->prepare("UPDATE buses SET bus_name = ?, bus_number = ?, total_seats = ? WHERE id = ? AND created_by_operator = ?");
        try {
            $updateStmt->execute([$bus_name, $bus_number, $total_seats, $bus_id, $operator_id]);
            header("Location: dashboard.php?msg=Bus+updated+successfully");
            exit;
        }
        catch (PDOException $e) {
            $error = "Error updating bus.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bus</title>
    <!-- Include same styles as add_bus.php for simplicity -->
    <style>
        body {
            font-family: sans-serif;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            padding-top: 50px;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #ffc107;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background: #e0a800;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <h2>Edit Bus</h2>

        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Bus Name</label>
                <input type="text" name="bus_name" value="<?= htmlspecialchars($bus['bus_name'])?>" required>
            </div>
            <div class="form-group">
                <label>Bus Number</label>
                <input type="text" name="bus_number" value="<?= htmlspecialchars($bus['bus_number'])?>" required>
            </div>
            <div class="form-group">
                <label>Total Seats</label>
                <input type="number" name="total_seats" min="1" max="100" value="<?= $bus['total_seats']?>" required>
            </div>
            <button type="submit">Update Bus</button>
        </form>
    </div>
</body>

</html>