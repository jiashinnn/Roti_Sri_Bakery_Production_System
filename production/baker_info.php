<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Get only bakers
    $stmt = $conn->query("SELECT 
                            user_id,
                            user_fullName,
                            user_role,
                            user_email,
                            user_contact,
                            user_address,
                            user_dateRegister
                         FROM tbl_users 
                         WHERE user_role = 'Baker'
                         ORDER BY user_fullName");
    $bakers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baker Info - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/baker.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Baker Information</h1>
            <div class="divider"></div>
        </div>

        <div class="baker-container">
            <div class="baker-header">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search bakers...">
                    <i class="fas fa-search"></i>
                </div>
                <button class="add-baker-btn">
                    <i class="fas fa-plus"></i> Add New Baker
                </button>
            </div>

            <div class="baker-grid">
                <?php if (empty($bakers)): ?>
                    <div class="no-bakers">
                        <p>No bakers found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($bakers as $baker): ?>
                        <div class="baker-card">
                            <div class="baker-info">
                                <div class="baker-header">
                                    <i class="fas fa-user user-icon"></i>
                                    <div class="role-badge baker">Baker</div>
                                </div>
                                <h3><?php echo htmlspecialchars($baker['user_fullName']); ?></h3>
                                <div class="contact-info">
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($baker['user_email']); ?></p>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($baker['user_contact']); ?></p>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($baker['user_address']); ?></p>
                                </div>
                                <div class="join-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    Joined: <?php echo date('M d, Y', strtotime($baker['user_dateRegister'])); ?>
                                </div>
                            </div>
                            <div class="baker-actions">
                                <button class="edit-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="delete-btn" title="Delete" 
                                        onclick="deleteBaker(<?php echo $baker['user_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/baker.js"></script>
</body>
</html> 