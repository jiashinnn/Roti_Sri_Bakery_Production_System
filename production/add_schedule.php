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

try {
    // Get all recipes
    $stmt = $conn->query("SELECT recipe_id, recipe_name FROM tbl_recipe ORDER BY recipe_name");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all users (bakers and supervisors)
    $stmt = $conn->query("SELECT user_id, user_fullName, user_role FROM tbl_users 
                         WHERE user_role IN ('Baker', 'Supervisor') 
                         ORDER BY user_role, user_fullName");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all equipment - show all equipment regardless of status
    $stmt = $conn->query("SELECT equipment_id, equipment_name, equipment_status 
                         FROM tbl_equipments 
                         ORDER BY equipment_name");
    $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn->beginTransaction();
        try {
            // Get schedule details
            $recipe_id = $_POST['recipe_id'];
            $schedule_date = $_POST['schedule_date'];
            $quantity = floatval($_POST['quantity']);
            $schedule_orderVolumn = intval($_POST['schedule_orderVolumn']);
            $assigned_users = $_POST['assigned_users'] ?? [];

            // Get selected equipment
            $selected_equipment = $_POST['equipment'] ?? [];

            // Insert schedule
            $stmt = $conn->prepare("INSERT INTO tbl_schedule 
                (recipe_id, schedule_date, schedule_quantityToProduce, schedule_status, schedule_orderVolumn) 
                VALUES (?, ?, ?, 'Pending', ?)");
            $stmt->execute([$recipe_id, $schedule_date, $quantity, $schedule_orderVolumn]);
            
            $schedule_id = $conn->lastInsertId();

            // Insert equipment assignments
            if (!empty($selected_equipment)) {
                $stmt = $conn->prepare("INSERT INTO tbl_schedule_equipment (schedule_id, equipment_id) VALUES (?, ?)");
                foreach ($selected_equipment as $equipment_id) {
                    $stmt->execute([$schedule_id, $equipment_id]);
                }
                
                // Update equipment status to 'In Use' in tbl_equipments
                $stmt = $conn->prepare("UPDATE tbl_equipments SET equipment_status = 'In Use' WHERE equipment_id = ?");
                foreach ($selected_equipment as $equipment_id) {
                    $stmt->execute([$equipment_id]);
                }
            }

            // Insert user assignments
            if (!empty($assigned_users)) {
                $stmt = $conn->prepare("INSERT INTO tbl_schedule_assignments (schedule_id, user_id) VALUES (?, ?)");
                foreach ($assigned_users as $user_id) {
                    $stmt->execute([$schedule_id, $user_id]);
                }
            }

            $conn->commit();
            $success_message = "Schedule created successfully!";

        } catch(PDOException $e) {
            $conn->rollBack();
            throw $e;
        }
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
    <title>Add Schedule - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/schedule.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Add New Schedule</h1>
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
                            <option value="<?php echo $recipe['recipe_id']; ?>">
                                <?php echo htmlspecialchars($recipe['recipe_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="schedule_date">Production Date</label>
                    <input type="date" id="schedule_date" name="schedule_date" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity to Produce</label>
                    <input type="number" id="quantity" name="quantity" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="schedule_orderVolumn">Order Volume (units)</label>
                    <input type="number" 
                           class="form-control" 
                           id="schedule_orderVolumn" 
                           name="schedule_orderVolumn" 
                           required 
                           min="1"
                           placeholder="Enter order volume">
                </div>
            </div>

            <div class="form-section">
                <h2>Equipment Selection</h2>
                <div class="equipment-selection">
                    <?php if (empty($equipment)): ?>
                        <p class="no-equipment">No equipment available at the moment.</p>
                    <?php else: ?>
                        <?php foreach ($equipment as $item): ?>
                            <label class="equipment-checkbox">
                                <input type="checkbox" 
                                       name="equipment[]" 
                                       value="<?php echo $item['equipment_id']; ?>">
                                <?php echo htmlspecialchars($item['equipment_name']); ?>
                                <span class="equipment-status <?php echo strtolower($item['equipment_status']); ?>">
                                    (<?php echo $item['equipment_status']; ?>)
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                            <input type="checkbox" name="assigned_users[]" value="<?php echo $user['user_id']; ?>">
                            <?php echo htmlspecialchars($user['user_fullName']); ?>
                        </label>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Create Schedule</button>
                <a href="view_schedules.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
</body>
</html> 