<?php
require_once 'config/db.php';
session_start();

$error = '';
$success = '';

if (!isset($_GET['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_GET['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    if (empty($code)) {
        $error = "Please enter the 6-digit verification code.";
    }
    else {
        $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE email = ? AND verification_code = ?");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['is_verified'] == 1) {
                $error = "Account is already verified.";
            }
            else {
                // Verify account
                $update = $pdo->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
                $update->execute([$user['id']]);

                header("Location: login.php?msg=Account+verified+successfully!+Please+log+in.");
                exit;
            }
        }
        else {
            $error = "Invalid verification code. Please check your email/SMS and try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account - Bus Ticket System</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="auth-container">
        <div class="container">
        <h2>Verify Your Account</h2>
        <p>We sent a 6-digit confirmation code to <strong>
                <?= htmlspecialchars($email)?>
            </strong>. Enter it below to activate your account.</p>

        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Verification Code</label>
                <input type="text" name="code" required maxlength="6" placeholder="000000" autocomplete="off">
            </div>
            <button type="submit">Verify & Activate</button>
        </form>
    </div>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>