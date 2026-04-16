<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$error = '';
$success = '';

// --- 1. Handle Account Creation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_staff'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }
    else {
        $email = strtolower($email);
        $checkRes = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' OR phone = '$phone'");
        if (mysqli_num_rows($checkRes) > 0) {
            $error = "A user with that email or phone number already exists.";
        }
        else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, phone, password, role, is_verified) 
                    VALUES ('$name', '$email', '$phone', '$hashedPassword', '$role', 1)";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Staff account created for $name.";
            }
            else {
                $error = "Error during account creation.";
            }
        }
    }
}

// --- 2. Fetch Existing Staff ---
$resStaff = mysqli_query($conn, "SELECT id, name, email, phone, role FROM users WHERE role IN ('operator', 'verifier') ORDER BY name ASC");
$staffList = [];
while ($row = mysqli_fetch_assoc($resStaff)) {
    $staffList[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - Mobus</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Super Admin Panel</h2>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="routes.php">Global Routes</a>
            <a href="create_user.php" class="active">Manage Staff</a>
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php"  class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">
        <a href="dashboard.php" class="back-link">&larr; Back to Dashboard</a>
        
        <div class="form-page-grid">
            <div class="panel">
                <h3>Add New Staff</h3>
                <?php if ($error): ?><div class="error"><?= htmlspecialchars($error)?></div><?php endif; ?>
                <?php if ($success): ?><div class="success"><?= htmlspecialchars($success)?></div><?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="create_staff" value="1">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="operator">Operator (Bus Manager)</option>
                            <option value="verifier">Verifier (Conductor)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-form-submit">Create Account</button>
                </form>
            </div>

            <div class="panel">
                <h3>Current Staff Members</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($staffList) > 0): ?>
                            <?php foreach ($staffList as $s): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($s['name'])?></strong><br>
                                    <small><?= htmlspecialchars($s['email'])?></small>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= $s['role']?>"><?= $s['role']?></span>
                                </td>
                                <td>
                                    <a href="edit_staff.php?id=<?= $s['id']?>" class="btn-view" style="font-size: 11px; padding: 4px 8px;">Edit</a>
                                    <a href="delete_staff.php?id=<?= $s['id']?>" class="btn-delete" style="font-size: 11px; padding: 4px 8px;" onclick="return confirm('Delete this staff member?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No staff accounts found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. PROVISIONING ACCOUNTS:
 * This page allows the Admin to create accounts for their staff (Operators and Verifiers). 
 * Unlike passengers who register themselves, staff accounts are created here.
 * 
 * 2. AUTOMATIC VERIFICATION:
 * We set `is_verified = 1` in the SQL query. This means staff members do not 
 * need to check their email for a verification code; they can log in immediately.
 * 
 * 3. UNIQUENESS CHECK:
 * Before creating the account, we check if the email or phone number is already 
 * in the database to prevent duplicate accounts.
 * 
 * 4. SECURITY:
 * Even though this is an admin page, we still use password_hash() to encrypt 
 * the staff passwords before saving them.
 */
?>