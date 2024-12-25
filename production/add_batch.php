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
    // Get recipes
    $stmt = $conn->query("SELECT recipe_id, recipe_name FROM tbl_recipe ORDER BY recipe_name");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get schedules
    $stmt = $conn->query("SELECT s.schedule_id, r.recipe_name, s.schedule_date 
                         FROM tbl_schedule s 
                         JOIN tbl_recipe r ON s.recipe_id = r.recipe_id 
                         WHERE s.schedule_status != 'Completed'
                         ORDER BY s.schedule_date DESC");
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bakers
    $stmt = $conn->query("SELECT user_id, user_fullName FROM tbl_users 
                         WHERE user_role = 'Baker' 
                         ORDER BY user_fullName");
    $bakers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn->beginTransaction();

        // Get batch details
        $recipe_id = $_POST['recipe_id'];
        $schedule_id = $_POST['schedule_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $remarks = $_POST['remarks'];
        $assignments = $_POST['assignments'] ?? [];

        // Insert batch
        $stmt = $conn->prepare("INSERT INTO tbl_batches (recipe_id, schedule_id, batch_startTime, 
                                                      batch_endTime, batch_remarks) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$recipe_id, $schedule_id, $start_time, $end_time, $remarks]);
        
        $batch_id = $conn->lastInsertId();

        // Insert task assignments
        if (!empty($assignments)) {
            $stmt = $conn->prepare("INSERT INTO tbl_batch_assignments 
                                  (batch_id, user_id, ba_task, ba_status) 
                                  VALUES (?, ?, ?, 'Pending')");
            
            foreach ($assignments as $assignment) {
                $stmt->execute([
                    $batch_id,
                    $assignment['user_id'],
                    $assignment['task']
                ]);
            }
        }

        $conn->commit();
        $success_message = "Batch created successfully!";

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
    <title>Add Batch - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/batch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Add New Batch</h1>
            <div class="divider"></div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" class="batch-form">
            <div class="form-section">
                <h2>Batch Details</h2>
                
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
                    <label for="schedule_id">Production Schedule</label>
                    <select id="schedule_id" name="schedule_id" required>
                        <option value="">Select Schedule</option>
                        <?php foreach ($schedules as $schedule): ?>
                            <option value="<?php echo $schedule['schedule_id']; ?>">
                                <?php echo htmlspecialchars($schedule['recipe_name'] . ' - ' . 
                                      date('M d, Y', strtotime($schedule['schedule_date']))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="datetime-local" id="end_time" name="end_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3"></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Task Assignments</h2>
                <div id="task-assignments">
                    <div class="task-assignment">
                        <div class="form-group">
                            <label>Baker</label>
                            <select name="assignments[0][user_id]" required>
                                <option value="">Select Baker</option>
                                <?php foreach ($bakers as $baker): ?>
                                    <option value="<?php echo $baker['user_id']; ?>">
                                        <?php echo htmlspecialchars($baker['user_fullName']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Task</label>
                            <select name="assignments[0][task]" required>
                                <option value="">Select Task</option>
                                <option value="Mixing">Mixing</option>
                                <option value="Baking">Baking</option>
                                <option value="Decorating">Decorating</option>
                            </select>
                        </div>
                        <button type="button" class="remove-task" onclick="removeTask(this)" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-task-btn" onclick="addTask()">
                    <i class="fas fa-plus"></i> Add Another Task
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Create Batch</button>
                <a href="view_batches.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/batch.js"></script>
</body>
</html> 