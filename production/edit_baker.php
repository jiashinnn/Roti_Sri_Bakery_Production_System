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
$fullName = $role = $email = $contact = $address = "";

// Get baker details
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE user_id = :edit_id");
        $stmt->execute([':edit_id' => $edit_id]);
        $baker = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($baker) {
            $fullName = $baker['user_fullName'];
            $role = $baker['user_role'];
            $email = $baker['user_email'];
            $contact = $baker['user_contact'];
            $address = $baker['user_address'];
        } else {
            $error_message = "Baker not found.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $role = htmlspecialchars($_POST['role']);
    $email = htmlspecialchars($_POST['email']);
    $contact = htmlspecialchars($_POST['contact']);
    $address = htmlspecialchars($_POST['address']);

    try {
        // Update baker details in the database
        $stmt = $conn->prepare("UPDATE tbl_users 
                                SET user_fullName = :fullName, user_role = :role, user_email = :email, user_contact = :contact, user_address = :address 
                                WHERE user_id = :edit_id");
        $stmt->execute([
            ':fullName' => $fullName,
            ':role' => $role,
            ':email' => $email,
            ':contact' => $contact,
            ':address' => $address,
            ':edit_id' => $edit_id
        ]);

        $success_message = "Baker details updated successfully!";
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
    <title>Edit Baker/Supervisor</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/add_baker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Edit Baker/Supervisor</h1>
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

            <!-- Edit Baker/Supervisor Form -->
            <form method="POST" action="">
                <div class="baker-form-section">
                    <h2>Personal Details</h2>
                    <div class="baker-form-group">
                        <label for="fullName">Full Name:</label>
                        <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($fullName); ?>" required>
                    </div>
                    <div class="baker-form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Baker" <?php echo $role == 'Baker' ? 'selected' : ''; ?>>Baker</option>
                            <option value="Supervisor" <?php echo $role == 'Supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                        </select>
                    </div>
                </div>

                <div class="baker-form-section">
                    <h2>Contact Information</h2>
                    <div class="baker-form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="baker-form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                    </div>
                </div>

                <div class="baker-form-section">
                    <h2>Address</h2>
                    <div class="baker-form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" required><?php echo htmlspecialchars($address); ?></textarea>
                    </div>
                </div>

                <div class="baker-form-actions">
                    <button type="submit" class="submit-btn">Update Baker/Supervisor</button>
                    <a href="baker_info.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
