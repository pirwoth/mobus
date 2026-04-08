<?php
require_once 'config/db.php';
require_once 'includes/auth_check.php';

if (isset($_SESSION['user_id'])) {
    redirectUserToDashboard($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = trim($_POST['email_or_phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email_or_phone) || empty($password)) {
        $error = "Please enter both Email/Phone and password.";
    }
    else {
        $stmt = $pdo->prepare("SELECT id, name, email, password, role, is_verified FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$email_or_phone, $email_or_phone]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 0) {
                // Not verified, redirect to verify.php
                header("Location: verify.php?email=" . urlencode($user['email']));
                exit;
            }

            // Password correct & verified, set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            redirectUserToDashboard($user['role']);
        }
        else {
            $error = "Invalid credentials.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bus Ticket System</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="auth-container">
        <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <?php if (isset($_GET['msg'])): ?>
        <div class="msg-success">
            <?= htmlspecialchars($_GET['msg'])?>
        </div>
        <?php
endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email or Phone Number</label>
                <input type="text" name="email_or_phone" required autofocus>
            </div>
            <div class="form-group position-relative">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required>
                    <span class="toggle-password" onclick="togglePassword('password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-eye">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
            </div>
            <button type="submit">Login</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">Don't have an account? <a href="register.php">Register</a></p>
    </div>

    <script>
        function togglePassword(inputId, iconSpan) {
            const input = document.getElementById(inputId);
            const svg = iconSpan.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                // Eye-off icon
                svg.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                // Eye icon
                svg.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>