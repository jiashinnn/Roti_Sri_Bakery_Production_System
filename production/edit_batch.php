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
    // Get batch ID from URL
    $batch_id = $_GET['id'] ?? null;
    if (!$batch_id) {
        header("Location: view_batches.php");
        exit();
    }

    // Get recipes
    $stmt = $conn->query("SELECT recipe_id, recipe_name FROM tbl_recipe ORDER BY recipe_name");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get schedules
    $stmt = $conn->query("SELECT s.schedule_id, r.recipe_name, s.schedule_date 
                         FROM tbl_schedule s 
                         JOIN tbl_recipe r ON s.recipe_id = r.recipe_id 
                         ORDER BY s.schedule_date DESC");
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bakers
    $stmt = $conn->query("SELECT user_id, user_fullName FROM tbl_users 
                         WHERE user_role = 'Baker' 
                         ORDER BY user_fullName");
    $bakers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get batch details
    $stmt = $conn->prepare("SELECT * FROM tbl_batches WHERE batch_id = ?");
    $stmt->execute([$batch_id]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$batch) {
        header("Location: view_batches.php");
        exit();
    }

    // Get existing assignments
    $stmt = $conn->prepare("SELECT * FROM tbl_batch_assignments WHERE batch_id = ?");
    $stmt->execute([$batch_id]);
    $existing_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn->beginTransaction();

        // Get batch details
        $recipe_id = $_POST['recipe_id'];
        $schedule_id = $_POST['schedule_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $status = $_POST['status'];
        $remarks = $_POST['remarks'];
        $assignments = $_POST['assignments'] ?? [];
        $quality_check = $_POST['quality_check'];

        // Update batch
        $stmt = $conn->prepare("UPDATE tbl_batches SET 
                                recipe_id = ?,
                                schedule_id = ?,
                                batch_startTime = ?,
                                batch_endTime = ?,
                                batch_status = ?,
                                batch_remarks = ?,
                                quality_check = ?
                              WHERE batch_id = ?");
        $stmt->execute([$recipe_id, $schedule_id, $start_time, $end_time, $status, $remarks, $quality_check, $batch_id]);

        // Delete existing assignments
        $stmt = $conn->prepare("DELETE FROM tbl_batch_assignments WHERE batch_id = ?");
        $stmt->execute([$batch_id]);

        // Insert new assignments
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
        $success_message = "Batch updated successfully!";

        // Refresh batch data
        $stmt = $conn->prepare("SELECT * FROM tbl_batches WHERE batch_id = ?");
        $stmt->execute([$batch_id]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);

        // Refresh assignments
        $stmt = $conn->prepare("SELECT * FROM tbl_batch_assignments WHERE batch_id = ?");
        $stmt->execute([$batch_id]);
        $existing_assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Edit Batch - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/batch.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Edit Batch</h1>
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
                            <option value="<?php echo $recipe['recipe_id']; ?>"
                                <?php echo $recipe['recipe_id'] == $batch['recipe_id'] ? 'selected' : ''; ?>>
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
                            <option value="<?php echo $schedule['schedule_id']; ?>"
                                <?php echo $schedule['schedule_id'] == $batch['schedule_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($schedule['recipe_name'] . ' - ' . 
                                      date('M d, Y', strtotime($schedule['schedule_date']))); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" 
                               value="<?php echo date('Y-m-d\TH:i', strtotime($batch['batch_startTime'])); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time</label>
                        <input type="datetime-local" id="end_time" name="end_time" 
                               value="<?php echo date('Y-m-d\TH:i', strtotime($batch['batch_endTime'])); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending" <?php echo $batch['batch_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo $batch['batch_status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo $batch['batch_status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3"><?php echo htmlspecialchars($batch['batch_remarks'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="quality_check">Quality Check Comments</label>
                    <textarea id="quality_check" name="quality_check" rows="3" 
                              placeholder="Enter quality check comments, production issues, or quantity concerns..."
                    ><?php echo htmlspecialchars($batch['quality_check'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2>Task Assignments</h2>
                <div id="task-assignments">
                    <?php foreach ($existing_assignments as $index => $assignment): ?>
                        <div class="task-assignment">
                            <div class="form-group">
                                <label>Baker</label>
                                <select name="assignments[<?php echo $index; ?>][user_id]" required>
                                    <option value="">Select Baker</option>
                                    <?php foreach ($bakers as $baker): ?>
                                        <option value="<?php echo $baker['user_id']; ?>"
                                            <?php echo $baker['user_id'] == $assignment['user_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($baker['user_fullName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Task</label>
                                <select name="assignments[<?php echo $index; ?>][task]" required>
                                    <option value="">Select Task</option>
                                    <option value="Mixing" <?php echo $assignment['ba_task'] === 'Mixing' ? 'selected' : ''; ?>>Mixing</option>
                                    <option value="Baking" <?php echo $assignment['ba_task'] === 'Baking' ? 'selected' : ''; ?>>Baking</option>
                                    <option value="Decorating" <?php echo $assignment['ba_task'] === 'Decorating' ? 'selected' : ''; ?>>Decorating</option>
                                </select>
                            </div>
                            <button type="button" class="remove-task" onclick="removeTask(this)" 
                                    <?php echo count($existing_assignments) === 1 ? 'style="display: none;"' : ''; ?>>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="add-task-btn" onclick="addTask()">
                    <i class="fas fa-plus"></i> Add Another Task
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Update Batch</button>
                <a href="view_batches.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/batch.js"></script>
</body>
</html> 