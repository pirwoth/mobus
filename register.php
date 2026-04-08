<?php
require_once 'config/db.php';
require_once 'includes/auth_check.php';

if (isset($_SESSION['user_id'])) {
    redirectUserToDashboard($_SESSION['role']);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Advanced Validations
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
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
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        $email = strtolower($email);

        // Uniqueness check (Email & Phone)
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $checkStmt->execute([$email, $phone]);
        if ($checkStmt->fetch()) {
            $error = "An account with that email or phone number already exists.";
        }
        else {
            // Hash password and generate 6-digit code
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = sprintf("%06d", mt_rand(1, 999999));
            $role = 'passenger'; // Forced to passenger

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, is_verified, verification_code) VALUES (?, ?, ?, ?, ?, 0, ?)");
            try {
                $stmt->execute([$name, $email, $phone, $hashedPassword, $role, $verification_code]);
                // Automatically redirect to verification page, passing email
                header("Location: verify.php?email=" . urlencode($email));
                exit;
            }
            catch (PDOException $e) {
                $error = "Error during registration. Ensure data is unique.";
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
    <title>Passenger Registration - Bus Ticket System</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="auth-container">
        <div class="container">
        <h2>Create an Account</h2>
        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <form method="POST">
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
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="reg_password" required>
                    <span class="toggle-password" onclick="togglePassword('reg_password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-eye">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <div class="hint">Min 8 chars: 1 letter, 1 number, 1 special char.</div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" name="confirm_password" id="reg_confirm" required>
                    <span class="toggle-password" onclick="togglePassword('reg_confirm', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-eye">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">Already have an account? <a href="login.php">Log In</a></p>
    </div>

    <script>
        function togglePassword(inputId, iconSpan) {
            const input = document.getElementById(inputId);
            const svg = iconSpan.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>