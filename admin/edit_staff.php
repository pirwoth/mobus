<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('admin');

$staff_id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';

// Fetch current details
$res = mysqli_query($conn, "SELECT * FROM users WHERE id = $staff_id AND role IN ('operator', 'verifier')");
$staff = mysqli_fetch_assoc($res);

if (!$staff) {
    header("Location: create_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (empty($name) || empty($email) || empty($phone) || empty($role)) {
        $error = "All fields are required.";
    } else {
        $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', role = '$role' 
                WHERE id = $staff_id";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Staff details updated successfully.";
            // Refresh local data
            $staff['name'] = $name;
            $staff['email'] = $email;
            $staff['phone'] = $phone;
            $staff['role'] = $role;
        } else {
            $error = "Error updating staff details.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - Mobus</title>
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
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout &mdash; <?= htmlspecialchars($_SESSION['name'])?></a>
        </div>
    </div>

    <div class="content">
        <a href="create_user.php" class="back-link">&larr; Back to Staff List</a>
        
        <div class="form-page-grid">
            <div class="panel">
                <h3>Edit Staff Member</h3>
                <p class="panel-desc">Update details for <strong><?= htmlspecialchars($staff['name']) ?></strong>.</p>

                <?php if ($error): ?><div class="error"><?= htmlspecialchars($error)?></div><?php endif; ?>
                <?php if ($success): ?><div class="success"><?= htmlspecialchars($success)?></div><?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="operator" <?= $staff['role'] === 'operator' ? 'selected' : '' ?>>Operator</option>
                            <option value="verifier" <?= $staff['role'] === 'verifier' ? 'selected' : '' ?>>Verifier</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($staff['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($staff['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($staff['phone']) ?>" required>
                    </div>
                    <button type="submit" class="btn-form-submit">Update Details</button>
                </form>
            </div>
        </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. EDITING STAFF:
 * This page is similar to adding staff, but it uses the SQL UPDATE command to 
 * modify an existing record based on its unique ID.
 * 
 * 2. PRE-FILLING:
 * We fetch the staff member's current data first so the user can see what 
 * they are changing.
 * 
 * 3. SECURITY:
 * We ensure that the ID passed in the URL belongs to a staff member (operator 
 * or verifier) to prevent tampering with other users.
 */
?>
