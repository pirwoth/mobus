<?php
require_once 'config/db.php';
require_once 'includes/auth_check.php';

if (isset($_SESSION['user_id'])) {
    redirectUserToDashboard($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protect against SQL injection by escaping all string inputs
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Step 1: Validations
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all the fields.";
    }
    elseif ($password !== $confirm_password) {
        $error = "The two passwords do not match.";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    }
    else {
        // Step 2: Check for existing account
        $checkSql = "SELECT id FROM users WHERE email = '$email' OR phone = '$phone'";
        $checkResult = mysqli_query($conn, $checkSql);
        
        if (mysqli_num_rows($checkResult) > 0) {
            $error = "An account with this email or phone already exists.";
        }
        else {
            // Step 3: Securely hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Generate a random 6-digit code for account verification
            $verification_code = rand(100000, 999999);
            $role = 'passenger'; 

            // Step 4: Insert new user
            $sql = "INSERT INTO users (name, email, phone, password, role, is_verified, verification_code) 
                    VALUES ('$name', '$email', '$phone', '$hashedPassword', '$role', 0, '$verification_code')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: verify.php?email=" . urlencode($email));
                exit;
            }
            else {
                $error = "An error occurred during registration. Please try again.";
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
    <title>Register - Mobus</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){
            // Apply the user's preferred theme (dark/light) from local storage
            var t = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", t);
        })();
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
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '')?>" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '')?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '')?>" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
                <div class="hint">Minimum 6 characters.</div>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit">Register Account</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Already have an account? <a href="login.php">Log In</a>
        </p>
    </div>
    </div>
    
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. ESCAPING INPUTS:
 * We use mysqli_real_escape_string() on all inputs ($name, $email, $phone). 
 * This prevents special characters from breaking our SQL query.
 * 
 * 2. UNIQUE ACCOUNT CHECK:
 * Before inserting, we check if the Email or Phone is already in use. 
 * mysqli_num_rows() tells us if any matches were found.
 * 
 * 3. SECURITY:
 * We use password_hash() to turn the password into a long string of random characters. 
 * This is "Safe Hashing" and is a industry standard.
 * 
 * 4. REDIRECT:
 * After a successful insert, header("Location: ...") sends the user to the verification page.
 */
?>