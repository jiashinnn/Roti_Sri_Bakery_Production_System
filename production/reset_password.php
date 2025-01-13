<?php
session_start();
require_once 'config/db_connection.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate token and expiry time
    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $expiryTime = strtotime($user['token_expiry']);
        $currentTime = time();

        if ($currentTime > $expiryTime) {
            $message = "Invalid or expired reset token.";
        } else {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];

                if ($password === $confirmPassword) {
                    // Hash the new password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // Update the password and clear the reset token
                    $stmt = $conn->prepare("UPDATE tbl_users SET user_password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
                    $stmt->execute([$hashedPassword, $token]);

                    $message = "Your password has been reset successfully!";
                } else {
                    $message = "Passwords do not match.";
                }
            }
        }
    } else {
        $message = "Invalid or expired reset token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <main>
        <div class="forgot-password-container">
            <h2>Reset Password</h2>
            <?php if ($message): ?>
                <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['token']) && $user): ?>
                <form method="POST" class="reset-form">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="password-field">
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="login-submit-btn">Reset Password</button>
                    <div class="form-links">
                        <a href="login.php" class="forgot-password">Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>
    <script src="js/reset_password.js"></script>
</body>
</html>
