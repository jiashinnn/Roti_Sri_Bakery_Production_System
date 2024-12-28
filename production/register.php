<?php
session_start();
require_once 'config/db_connection.php';

$error_message = '';
$success_message = '';
$fullname = $contact = $email = $address = '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data and sanitize inputs
        $fullname = htmlspecialchars(trim($_POST['fullname']));
        $contact = htmlspecialchars(trim($_POST['contact']));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $address = htmlspecialchars(trim($_POST['address']));
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $date_register = date('Y-m-d H:i:s');


        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     throw new Exception("Invalid email format.");
        // }

        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            throw new Exception("Password must be at least 8 characters long and include at least ONE uppercase letter, ONE lowercase letter, ONE number, and ONE special character.");
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_email FROM tbl_users WHERE user_email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered!");
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO tbl_users (user_fullName, user_contact, user_address, user_email, user_password, user_dateRegister) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullname, $contact, $address, $email, $hashed_password, $date_register]);

            // Show success message as an alert
            echo "<script>
                alert('Registration successful! Please login.');
                window.location.href = 'login.php';
            </script>";
            exit;
        }
    } catch (PDOException $e) {
        // Show database error as an alert
        echo "<script>
            alert('Database Error: " . addslashes($e->getMessage()) . "');
        </script>";
    } catch (Exception $e) {
        // Show validation or general error as an alert
        echo "<script>
            alert('" . addslashes($e->getMessage()) . "');
        </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <header>
        <nav class="nav-container">
            <a href="homepage.php" class="logo">
                <img src="assets/images/logo_name_w.png" alt="YSL Logo">
            </a>
            <ul class="nav-links">
                <li><a href="homepage.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="register-container">
            <h2>Create Account</h2>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <form method="POST" class="register-form">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                </div>

                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="tel" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-field">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="toggle-password" data-target="confirm_password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="register-submit-btn">Register</button>
            </form>
        </div>
    </main>

    <footer>
        <p class="footer-text">&copy; 2024 YSLProduction | Production System</p>
        <img src="assets/images/footer.png" alt="YSL Production Logo" class="footer-logo">
    </footer>
    <script src="js/register.js"></script>
</body>

</html>