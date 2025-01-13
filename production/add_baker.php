<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$success_message = $error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $dateRegister = date('Y-m-d H:i:s');

    try {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO tbl_users 
                                (user_fullName, user_role, user_email, user_contact, user_address, user_password, user_dateRegister) 
                                VALUES (:fullName, :role, :email, :contact, :address, :password, :dateRegister)");
        $stmt->execute([
            ':fullName' => $fullName,
            ':role' => $role,
            ':email' => $email,
            ':contact' => $contact,
            ':address' => $address,
            ':password' => $password,
            ':dateRegister' => $dateRegister
        ]);

        $success_message = "New $role added successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize messages
$success_message = $error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $dateRegister = date('Y-m-d H:i:s');

    try {
        $stmt = $conn->prepare("INSERT INTO tbl_users 
                                (user_fullName, user_role, user_email, user_contact, user_address, user_password, user_dateRegister) 
                                VALUES (:fullName, :role, :email, :contact, :address, :password, :dateRegister)");
        $stmt->execute([
            ':fullName' => $fullName,
            ':role' => $role,
            ':email' => $email,
            ':contact' => $contact,
            ':address' => $address,
            ':password' => $password,
            ':dateRegister' => $dateRegister
        ]);

        $success_message = "New $role added successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Baker/Supervisor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/add_baker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Add Baker/Supervisor</h1>
            <div class="divider"></div>
        </div>

        <!-- Form Container -->
        <div class="baker-form-container">
            <!-- Display Messages -->
            <?php if ($success_message): ?>
                <div class="alert success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Add Baker/Supervisor Form -->
            <form method="POST" action="">
                <div class="baker-form-section">
                    <h2>Personal Details</h2>
                    <div class="baker-form-group">
                        <label for="fullName">Full Name:</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter full name" required>
                    </div>
                    <div class="baker-form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Baker">Baker</option>
                            <option value="Supervisor">Supervisor</option>
                        </select>
                    </div>
                </div>

                <div class="baker-form-section">
                    <h2>Contact Information</h2>
                    <div class="baker-form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter email address" required>
                    </div>
                    <div class="baker-form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" id="contact" name="contact" placeholder="Enter contact number" required>
                    </div>
                </div>

                <div class="baker-form-section">
                    <h2>Address</h2>
                    <div class="baker-form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" placeholder="Enter address" required></textarea>
                    </div>
                </div>

                <div class="baker-form-section">
                    <h2>Account Information</h2>
                    <div class="baker-form-group">
                        <label for="password">Password:</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password"
                                pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}"
                                title="Password must contain at least 8 characters, including uppercase, lowercase, a number, and a special character."
                                required minlength="8" style="padding-right: 2.5rem;">
                            <button type="button" id="togglePassword" class="eye-btn" style="position: absolute; right: 0.5rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="baker-form-actions">
                    <button type="submit" class="submit-btn">Add Baker/Supervisor</button>
                    <a href="baker_info.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
