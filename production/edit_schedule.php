<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Check if schedule ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_schedules.php");
    exit();
}

$schedule_id = $_GET['id'];

try {
    // Get schedule details
    $stmt = $conn->prepare("SELECT * FROM tbl_schedule WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$schedule) {
        header("Location: view_schedules.php");
        exit();
    }

    // Get all recipes
    $stmt = $conn->query("SELECT recipe_id, recipe_name FROM tbl_recipe ORDER BY recipe_name");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all users (bakers and supervisors)
    $stmt = $conn->query("SELECT user_id, user_fullName, user_role FROM tbl_users 
                         WHERE user_role IN ('Baker', 'Supervisor') 
                         ORDER BY user_role, user_fullName");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get assigned users
    $stmt = $conn->prepare("SELECT user_id FROM tbl_schedule_assignments WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    $assigned_users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn->beginTransaction();

        // Get schedule details
        $recipe_id = $_POST['recipe_id'];
        $schedule_date = $_POST['schedule_date'];
        $quantity = floatval($_POST['quantity']);
        $status = $_POST['status'];
        $assigned_users_new = $_POST['assigned_users'] ?? [];

        // Update schedule
        $stmt = $conn->prepare("UPDATE tbl_schedule SET 
                              recipe_id = ?, 
                              schedule_date = ?, 
                              schedule_quantityToProduce = ?,
                              schedule_status = ?
                              WHERE schedule_id = ?");
        $stmt->execute([$recipe_id, $schedule_date, $quantity, $status, $schedule_id]);

        // Delete existing assignments
        $stmt = $conn->prepare("DELETE FROM tbl_schedule_assignments WHERE schedule_id = ?");
        $stmt->execute([$schedule_id]);

        // Insert new assignments
        if (!empty($assigned_users_new)) {
            $stmt = $conn->prepare("INSERT INTO tbl_schedule_assignments (schedule_id, user_id) VALUES (?, ?)");
            foreach ($assigned_users_new as $user_id) {
                $stmt->execute([$schedule_id, $user_id]);
            }
        }

        $conn->commit();
        $success_message = "Schedule updated successfully!";

        // Refresh schedule data
        $stmt = $conn->prepare("SELECT * FROM tbl_schedule WHERE schedule_id = ?");
        $stmt->execute([$schedule_id]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

        // Refresh assigned users
        $stmt = $conn->prepare("SELECT user_id FROM tbl_schedule_assignments WHERE schedule_id = ?");
        $stmt->execute([$schedule_id]);
        $assigned_users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    }
} catch(PDOException $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Edit Schedule</h1>
            <div class="divider"></div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" class="schedule-form">
            <div class="form-section">
                <h2>Schedule Details</h2>
                
                <div class="form-group">
                    <label for="recipe_id">Recipe</label>
                    <select id="recipe_id" name="recipe_id" required>
                        <option value="">Select Recipe</option>
                        <?php foreach ($recipes as $recipe): ?>
                            <option value="<?php echo $recipe['recipe_id']; ?>" 
                                <?php echo $schedule['recipe_id'] == $recipe['recipe_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($recipe['recipe_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="schedule_date">Production Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" 
                           value="<?php echo $schedule['schedule_date']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity to Produce</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" 
                           value="<?php echo $schedule['schedule_quantityToProduce']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?php echo $schedule['schedule_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo $schedule['schedule_status'] == 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo $schedule['schedule_status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h2>Assign Users</h2>
                <div class="user-assignments">
                    <?php
                    $current_role = '';
                    foreach ($users as $user):
                        if ($current_role != $user['user_role']):
                            if ($current_role != '') echo '</div>';
                            $current_role = $user['user_role'];
                    ?>
                        <h3><?php echo $current_role; ?>s</h3>
                        <div class="user-group">
                    <?php endif; ?>
                        <label class="user-checkbox">
                            <input type="checkbox" name="assigned_users[]" 
                                   value="<?php echo $user['user_id']; ?>"
                                   <?php echo in_array($user['user_id'], $assigned_users) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($user['user_fullName']); ?>
                        </label>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Update Schedule</button>
                <a href="view_schedules.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
</body>
</html> 