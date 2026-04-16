<?php
require_once 'config/db.php';
require_once 'includes/auth_check.php';

if (isset($_SESSION['user_id'])) {
    redirectUserToDashboard($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_or_phone = mysqli_real_escape_string($conn, $_POST['email_or_phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email_or_phone) || empty($password)) {
        $error = "Please enter both Email/Phone and password.";
    }
    else {
        // Step 1: Look for user
        $sql = "SELECT id, name, email, password, role, is_verified FROM users WHERE email = '$email_or_phone' OR phone = '$email_or_phone'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);

        // Step 2: Verify password
        if ($user && password_verify($password, $user['password'])) {
            
            if ($user['is_verified'] == 0) {
                header("Location: verify.php?email=" . urlencode($user['email']));
                exit;
            }

            // Step 3: Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            redirectUserToDashboard($user['role']);
        }
        else {
            $error = "Invalid login details. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mobus</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">

    <script>
        (function(){
            var savedTheme = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", savedTheme);
        })();
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
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
        <div class="msg-success">
            <?= htmlspecialchars($_GET['msg'])?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email or Phone Number</label>
                <input type="text" name="email_or_phone" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required>
                    <span class="toggle-password" onclick="togglePassword()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
            </div>

            <button type="submit">Login Now</button>
        </form>

        <p style="text-align: center; margin-top: 15px;">
            Don't have an account? <a href="register.php">Create Account</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            var p = document.getElementById("password");
            p.type = (p.type === "password") ? "text" : "password";
        }
    </script>
    </div>

    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. REDIRECT CHECK:
 * If the user is already logged in (session exists), we automatically send them to their dashboard.
 * 
 * 2. SQL WITH MYSQLI:
 * We use mysqli_real_escape_string() to clean the user input before putting it into the SQL query.
 * This is a basic security step to prevent SQL Injection.
 * 
 * 3. PASSWORD VERIFY:
 * We fetch the hashed password from the database and use password_verify() to see if it matches 
 * the text the user typed in.
 * 
 * 4. SESSIONS:
 * If login is successful, we store the user's ID, Name, and Role in $_SESSION. 
 * This allows other pages to know who is logged in.
 */
?>