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

// Check if recipe ID is provided
if (!isset($_GET['id'])) {
    header("Location: view_recipes.php");
    exit();
}

$recipe_id = $_GET['id'];

// Fetch recipe and ingredients data
try {
    // Get recipe details
    $stmt = $conn->prepare("SELECT * FROM tbl_recipe WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipe) {
        header("Location: view_recipes.php");
        exit();
    }

    // Get ingredients
    $stmt = $conn->prepare("SELECT * FROM tbl_ingredients WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        // Update recipe details
        $stmt = $conn->prepare("UPDATE tbl_recipe SET 
            recipe_name = ?, 
            recipe_category = ?, 
            recipe_batchSize = ?, 
            recipe_unitOfMeasure = ?,
            recipe_instructions = ?
            WHERE recipe_id = ?");
        
        $stmt->execute([
            htmlspecialchars(trim($_POST['recipe_name'])),
            htmlspecialchars(trim($_POST['recipe_category'])),
            floatval($_POST['batch_size']),
            htmlspecialchars(trim($_POST['unit_of_measure'])),
            htmlspecialchars(trim($_POST['recipe_instructions'])),
            $recipe_id
        ]);

        // Delete existing ingredients
        $stmt = $conn->prepare("DELETE FROM tbl_ingredients WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);

        // Insert updated ingredients
        $stmt = $conn->prepare("INSERT INTO tbl_ingredients (recipe_id, ingredient_name, ingredient_quantity, ingredient_unitOfMeasure) VALUES (?, ?, ?, ?)");
        
        $ingredient_names = $_POST['ingredient_name'];
        $ingredient_quantities = $_POST['ingredient_quantity'];
        $ingredient_units = $_POST['ingredient_unit'];

        foreach ($ingredient_names as $key => $name) {
            if (!empty($name)) {
                $stmt->execute([
                    $recipe_id,
                    htmlspecialchars(trim($name)),
                    floatval($ingredient_quantities[$key]),
                    htmlspecialchars(trim($ingredient_units[$key]))
                ]);
            }
        }

        $conn->commit();
        $success_message = "Recipe updated successfully!";
        
        // Refresh recipe data
        $stmt = $conn->prepare("SELECT * FROM tbl_recipe WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("SELECT * FROM tbl_ingredients WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch(PDOException $e) {
        $conn->rollBack();
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/add_recipe.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Edit Recipe</h1>
            <div class="divider"></div>
        </div>

        <?php if ($success_message): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" class="recipe-form">
            <div class="form-section">
                <h2>Recipe Details</h2>
                <div class="form-group">
                    <label for="recipe_name">Recipe Name</label>
                    <input type="text" id="recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe['recipe_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="recipe_category">Category</label>
                    <select id="recipe_category" name="recipe_category" required>
                        <option value="">Select Category</option>
                        <option value="Bread" <?php echo $recipe['recipe_category'] == 'Bread' ? 'selected' : ''; ?>>Bread</option>
                        <option value="Cake" <?php echo $recipe['recipe_category'] == 'Cake' ? 'selected' : ''; ?>>Cake</option>
                        <option value="Pastry" <?php echo $recipe['recipe_category'] == 'Pastry' ? 'selected' : ''; ?>>Pastry</option>
                        <option value="Cookie" <?php echo $recipe['recipe_category'] == 'Cookie' ? 'selected' : ''; ?>>Cookie</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="batch_size">Batch Size</label>
                        <input type="number" id="batch_size" name="batch_size" step="0.01" value="<?php echo $recipe['recipe_batchSize']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="unit_of_measure">Unit of Measure</label>
                        <select id="unit_of_measure" name="unit_of_measure" required>
                            <option value="">Select Unit</option>
                            <option value="pcs" <?php echo $recipe['recipe_unitOfMeasure'] == 'pcs' ? 'selected' : ''; ?>>Pieces</option>
                            <option value="kg" <?php echo $recipe['recipe_unitOfMeasure'] == 'kg' ? 'selected' : ''; ?>>Kilograms</option>
                            <option value="g" <?php echo $recipe['recipe_unitOfMeasure'] == 'g' ? 'selected' : ''; ?>>Grams</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Add Recipe Instructions Section -->
            <div class="form-section">
                <h2>Recipe Instructions</h2>
                <div class="form-group">
                    <label for="recipe_instructions">Instructions (Enter each step on a new line)</label>
                    <textarea 
                        id="recipe_instructions" 
                        name="recipe_instructions" 
                        rows="10" 
                        required
                    ><?php echo htmlspecialchars($recipe['recipe_instructions']); ?></textarea>
                    <small class="form-text">Enter each instruction step on a new line, preferably numbered.</small>
                </div>
            </div>

            <div class="form-section">
                <h2>Ingredients</h2>
                <div id="ingredients-container">
                    <?php foreach ($ingredients as $ingredient): ?>
                        <div class="ingredient-row">
                            <div class="form-group">
                                <label>Ingredient Name</label>
                                <input type="text" name="ingredient_name[]" value="<?php echo htmlspecialchars($ingredient['ingredient_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" name="ingredient_quantity[]" step="0.01" value="<?php echo $ingredient['ingredient_quantity']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Unit</label>
                                <select name="ingredient_unit[]" required>
                                    <option value="">Select Unit</option>
                                    <option value="kg" <?php echo $ingredient['ingredient_unitOfMeasure'] == 'kg' ? 'selected' : ''; ?>>Kilograms</option>
                                    <option value="g" <?php echo $ingredient['ingredient_unitOfMeasure'] == 'g' ? 'selected' : ''; ?>>Grams</option>
                                    <option value="l" <?php echo $ingredient['ingredient_unitOfMeasure'] == 'l' ? 'selected' : ''; ?>>Liters</option>
                                    <option value="ml" <?php echo $ingredient['ingredient_unitOfMeasure'] == 'ml' ? 'selected' : ''; ?>>Milliliters</option>
                                    <option value="pcs" <?php echo $ingredient['ingredient_unitOfMeasure'] == 'pcs' ? 'selected' : ''; ?>>Pieces</option>
                                </select>
                            </div>
                            <button type="button" class="remove-ingredient" onclick="removeIngredient(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="add-ingredient" onclick="addIngredient()">
                    <i class="fas fa-plus"></i> Add Ingredient
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Update Recipe</button>
                <a href="view_recipes.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/add_recipe.js"></script>
</body>
</html> 