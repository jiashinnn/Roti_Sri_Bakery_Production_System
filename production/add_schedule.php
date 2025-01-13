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
$recipes = [];
$equipment = [];
$users = [];

try {
    // Get all recipes with their batch sizes
    $stmt = $conn->query("SELECT recipe_id, recipe_name, recipe_batchSize FROM tbl_recipe ORDER BY recipe_name");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $selected_equipment = $_POST['equipment'] ?? [];
            $schedule_batchNum = $_POST['schedule_batchNum'];

            // Insert schedule
            $stmt = $conn->prepare("INSERT INTO tbl_schedule 
                (recipe_id, schedule_date, schedule_quantityToProduce, schedule_status, schedule_orderVolumn, schedule_batchNum) 
                VALUES (?, ?, ?, 'Pending', ?, ?)");
            $stmt->execute([$recipe_id, $schedule_date, $quantity, $schedule_orderVolumn, $schedule_batchNum]);
            
            $schedule_id = $conn->lastInsertId();

            // Insert equipment assignments
            if (!empty($selected_equipment)) {
                $stmt = $conn->prepare("INSERT INTO tbl_schedule_equipment (schedule_id, equipment_id) VALUES (?, ?)");
                foreach ($selected_equipment as $equipment_id) {
                    $stmt->execute([$schedule_id, $equipment_id]);
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

    // Handle AJAX requests for user availability
    if (isset($_GET['date'])) {
        $selected_date = $_GET['date'];

        $stmt = $conn->prepare("
            SELECT u.user_id, u.user_fullName, u.user_role,
                   CASE 
                       WHEN EXISTS (
                           SELECT 1 
                           FROM tbl_schedule_assignments sa 
                           JOIN tbl_schedule s ON sa.schedule_id = s.schedule_id 
                           WHERE sa.user_id = u.user_id 
                           AND DATE(s.schedule_date) = ?
                       ) THEN 'Unavailable'
                       ELSE 'Available'
                   END AS availability_status
            FROM tbl_users u
            WHERE u.user_role IN ('Baker', 'Supervisor')
            ORDER BY u.user_role, u.user_fullName
        ");
        $stmt->execute([$selected_date]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($users);
        exit();
    }

    // Handle AJAX requests for equipment availability
    if (isset($_GET['date_equipment'])) {
        $selected_date = $_GET['date_equipment'];
    
        $stmt = $conn->prepare("
            SELECT e.equipment_id, e.equipment_name,
                   CASE 
                       WHEN EXISTS (
                           SELECT 1 
                           FROM tbl_schedule_equipment se 
                           JOIN tbl_schedule s ON se.schedule_id = s.schedule_id 
                           WHERE se.equipment_id = e.equipment_id 
                           AND DATE(s.schedule_date) = ?
                       ) THEN 'In-Use'
                       ELSE 'Available'
                   END AS availability_status
            FROM tbl_equipments e
            ORDER BY e.equipment_name
        ");
        $stmt->execute([$selected_date]);
        $equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        header('Content-Type: application/json');
        echo json_encode($equipment);
        exit();
    }
    
} catch(PDOException $e) {
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
    <style>
    .calculation-info {
        display: block;
        color: #666;
        font-size: 0.9em;
        margin-top: 5px;
        font-style: italic;
    }
    
    .input-with-button {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .calculate-btn {
        padding: 8px 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .calculate-btn:hover {
        background-color: #45a049;
    }
    
    .calculate-btn i {
        font-size: 0.9em;
    }
    </style>
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
                            <option value="<?php echo $recipe['recipe_id']; ?>" 
                                    data-batch-size="<?php echo $recipe['recipe_batchSize']; ?>">
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
                    <label for="schedule_orderVolumn">Order Volume (units)</label>
                    <div class="input-with-button">
                        <input type="number" id="schedule_orderVolumn" name="schedule_orderVolumn" 
                               required min="1">
                        <button type="button" id="calculateBtn" class="calculate-btn">
                            <i class="fas fa-calculator"></i> Calculate
                        </button>
                    </div>
                    <small id="calculation-info" class="calculation-info"></small>
                </div>

                <div class="form-group">
                    <label for="schedule_batchNum">Number of Batch:</label>
                    <input type="number" 
                           id="schedule_batchNum" 
                           name="schedule_batchNum" 
                           class="form-control" 
                           min="1" 
                           readonly>
                    <small id="batch-calculation" class="calculation-info"></small>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity to Produce</label>
                    <input type="number" id="quantity" name="quantity" 
                           step="0.01" min="0.01" required readonly>
                    <small id="quantity-calculation" class="calculation-info"></small>
                </div>
            </div>

            <div class="form-section">
                <h2>Equipment Selection</h2>
                <div id="equipment-selection" class="equipment-selection">
                    <p>Select a production date to see equipment availability.</p>
                </div>
            </div>


            <div class="form-section">
                <h2>Assign Users</h2>
                <div id="user-availability" class="user-selection">
                    <p>Select a production date to see user availability.</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Create Schedule</button>
                <a href="view_schedules.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/schedule.js"></script>
    <script>
    function calculateBatchAndQuantity() {
        const recipeSelect = document.getElementById('recipe_id');
        const orderVolume = document.getElementById('schedule_orderVolumn').value;
        const selectedOption = recipeSelect.options[recipeSelect.selectedIndex];
        
        // Clear previous calculation info
        document.getElementById('calculation-info').innerHTML = '';
        document.getElementById('batch-calculation').innerHTML = '';
        document.getElementById('quantity-calculation').innerHTML = '';
        
        if (orderVolume && selectedOption.value) {
            const batchSize = parseFloat(selectedOption.getAttribute('data-batch-size'));
            const recipeName = selectedOption.text;
            
            // Calculate number of batches (rounded up)
            const rawBatches = orderVolume / batchSize;
            const numBatches = Math.ceil(rawBatches);
            
            // Calculate actual quantity to produce
            const quantity = numBatches * batchSize;
            
            // Update the form fields
            document.getElementById('schedule_batchNum').value = numBatches;
            document.getElementById('quantity').value = quantity;
            
            // Show calculation details
            document.getElementById('calculation-info').innerHTML = 
                `Selected recipe: ${recipeName} (Batch size: ${batchSize} units)`;
            
            document.getElementById('batch-calculation').innerHTML = 
                `${orderVolume} units ÷ ${batchSize} units per batch = ${rawBatches.toFixed(2)} → Rounded up to ${numBatches} batches`;
            
            document.getElementById('quantity-calculation').innerHTML = 
                `${numBatches} batches × ${batchSize} units per batch = ${quantity} units total`;
        } else {
            // Clear the fields if no recipe is selected or no order volume entered
            document.getElementById('schedule_batchNum').value = '';
            document.getElementById('quantity').value = '';
        }
    }

    // Add event listeners
    document.getElementById('calculateBtn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent form submission
        calculateBatchAndQuantity();
    });
    
    // Also calculate when Enter is pressed in the order volume field
    document.getElementById('schedule_orderVolumn').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent form submission
            calculateBatchAndQuantity();
        }
    });
    </script>
</body>
</html>
