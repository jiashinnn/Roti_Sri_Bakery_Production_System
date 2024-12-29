<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn->beginTransaction();

        // Get recipe details
        $recipe_name = htmlspecialchars(trim($_POST['recipe_name']));
        $recipe_category = htmlspecialchars(trim($_POST['recipe_category']));
        $recipe_batchSize = floatval($_POST['batch_size']);
        $recipe_unitOfMeasure = htmlspecialchars(trim($_POST['unit_of_measure']));

        // Insert recipe first
        $stmt = $conn->prepare("INSERT INTO tbl_recipe (recipe_name, recipe_category, recipe_batchSize, recipe_unitOfMeasure) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$recipe_name, $recipe_category, $recipe_batchSize, $recipe_unitOfMeasure]);

        // Get the ID of the recipe we just inserted
        $recipe_id = $conn->lastInsertId();

        // Now insert ingredients using the recipe_id
        $ingredient_names = $_POST['ingredient_name'];
        $ingredient_quantities = $_POST['ingredient_quantity'];
        $ingredient_units = $_POST['ingredient_unit'];

        $stmt = $conn->prepare("INSERT INTO tbl_ingredients (recipe_id, ingredient_name, ingredient_quantity, ingredient_unitOfMeasure) 
                              VALUES (?, ?, ?, ?)");

        foreach ($ingredient_names as $key => $name) {
            if (!empty($name)) {
                $ingredient_quantity = floatval($ingredient_quantities[$key]);
                $stmt->execute([
                    $recipe_id,  // First parameter
                    htmlspecialchars(trim($name)),  // Second parameter
                    $ingredient_quantity, // Third parameter
                    htmlspecialchars(trim($ingredient_units[$key]))  // Fourth parameter
                ]);
            }
        }

        $conn->commit();
        $success_message = "Recipe added successfully!";
    } catch (PDOException $e) {
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
    <title>Add Recipe - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/add_recipe.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Add New Recipe</h1>
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
                    <input type="text" id="recipe_name" name="recipe_name" required>
                </div>

                <div class="form-group">
                    <label for="recipe_category">Category</label>
                    <select id="recipe_category" name="recipe_category" required>
                        <option value="">Select Category</option>
                        <option value="Bread">Bread</option>
                        <option value="Cake">Cake</option>
                        <option value="Pastry">Pastry</option>
                        <option value="Cookie">Cookie</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="batch_size">Batch Size</label>
                        <input type="number" id="batch_size" name="batch_size" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="unit_of_measure">Unit of Measure</label>
                        <select id="unit_of_measure" name="unit_of_measure" required>
                            <option value="">Select Unit</option>
                            <option value="pcs">Pieces</option>
                            <option value="kg">Kilograms</option>
                            <option value="g">Grams</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Ingredients</h2>
                <div id="ingredients-container">
                    <div class="ingredient-row">
                        <div class="form-group">
                            <label>Ingredient Name</label>
                            <input type="text" name="ingredient_name[]" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" name="ingredient_quantity[]" step="0.01" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Unit</label>
                            <select name="ingredient_unit[]" required>
                                <option value="">Select Unit</option>
                                <option value="kg">Kilograms</option>
                                <option value="g">Grams</option>
                                <option value="l">Liters</option>
                                <option value="ml">Milliliters</option>
                                <option value="pcs">Pieces</option>
                            </select>
                        </div>
                        <button type="button" class="remove-ingredient" onclick="removeIngredient(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="add-ingredient" onclick="addIngredient()">
                    <i class="fas fa-plus"></i> Add Ingredient
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="submit-btn">Save Recipe</button>
            </div>
        </form>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/add_recipe.js"></script>
</body>

</html>