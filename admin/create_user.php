<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Advanced Validations
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    }
    elseif (strlen($name) < 3 || strlen($name) > 100 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Full Name must be 3-100 characters containing only letters and spaces.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    elseif (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $error = "Phone strictly must be 10-15 digits.";
    }
    elseif (strlen($password) < 8 || !preg_match("/[a-zA-Z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[^a-zA-Z0-9]/", $password)) {
        $error = "Password must be at least 8 characters and include a letter, number, and special character.";
    }
    elseif (!in_array($role, ['operator', 'verifier'])) {
        $error = "Invalid role selected.";
    }
    else {
        $email = strtolower($email);

        // Uniqueness check
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $checkStmt->execute([$email, $phone]);
        if ($checkStmt->fetch()) {
            $error = "A user with that email or phone number already exists.";
        }
        else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = null;
            // Staff are provisioned by admin, so they are born verified
            $is_verified = 1;

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, is_verified, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $phone, $hashedPassword, $role, $is_verified, $verification_code]);
                $success = "Staff account specifically provisioned for {$name} ({$role}).";
            }
            catch (PDOException $e) {
                $error = "Error during provisioning. Ensure data is unique.";
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
    <title>Provision Staff - Super Admin</title>
    
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
            <a href="routes.php">Global Routes</a>
            <a href="create_user.php" class="active">Provision Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        <div class="panel">
            <h3>Provision New Staff Account</h3>
            <p class="panel-desc">Use this form to securely provision Operator or Ticket Verifier accounts. Accounts created here are automatically marked as verified.</p>

            <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error)?>
            </div>
            <?php
endif; ?>
            <?php if ($success): ?>
            <div class="success">
                <?= htmlspecialchars($success)?>
            </div>
            <?php
endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Role</label>
                    <select name="role" required>
                        <option value="">-- Select Staff Role --</option>
                        <option value="operator">Operator (Bus/Trip Manager)</option>
                        <option value="verifier">Ticket Verifier (Conductor)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '')?>" required>
                    <div class="hint">3-100 characters, letters only.</div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '')?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '')?>" required>
                    <div class="hint">10-15 digits. No spaces or dashes.</div>
                </div>
                <div class="form-group">
                    <label>Assign Temporary Password</label>
                    <input type="password" name="password" required>
                    <div class="hint">Min 8 chars: 1 letter, 1 number, 1 special char.</div>
                </div>
                <button type="submit">Create Staff Account</button>
            </form>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>