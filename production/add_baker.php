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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Baker/Supervisor - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/baker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Add Baker/Supervisor</h1>
            <div class="divider"></div>
        </div>

        <!-- Display Success or Error Message -->
        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add Baker/Supervisor Form -->
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="fullName">Full Name:</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="Baker">Baker</option>
                        <option value="Supervisor">Supervisor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter email address" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter contact number" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" placeholder="Enter address" required></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="submit-btn">Add Baker/Supervisor</button>
            </form>
        </div>
    </main>

    <script src="js/dashboard.js"></script>
</body>
</html>
