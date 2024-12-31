<?php
session_start();
require_once 'config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all recipes with their ingredients
try {
    // Get all recipes
    $stmt = $conn->prepare("SELECT * FROM tbl_recipe ORDER BY recipe_dateCreated DESC");
    $stmt->execute();
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get ingredients for each recipe
    $recipe_ingredients = [];
    $stmt = $conn->prepare("SELECT * FROM tbl_ingredients WHERE recipe_id = ?");
    
    foreach ($recipes as $recipe) {
        $stmt->execute([$recipe['recipe_id']]);
        $recipe_ingredients[$recipe['recipe_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>View Recipes - YSLProduction</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/view_recipes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/dashboard_navigation.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1>Recipe List</h1>
            <div class="divider"></div>
        </div>

        <div class="recipes-container">
            <?php if (isset($error_message)): ?>
                <div class="alert error"><?php echo $error_message; ?></div>
            <?php else: ?>
                <div class="table-actions">
                    <a href="add_recipe.php" class="add-btn">
                        <i class="fas fa-plus"></i> Add New Recipe
                    </a>
                </div>
                <?php if (empty($recipes)): ?>
                    <div class="no-recipes">
                        <p>No recipes found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="recipes-table">
                            <thead>
                                <tr>
                                    <th>Recipe Name</th>
                                    <th>Category</th>
                                    <th>Batch Size</th>
                                    <th>Ingredients</th>
                                    <th>Instructions</th>
                                    <th>Date Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recipes as $recipe): ?>
                                    <tr data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                                        <td><?php echo htmlspecialchars($recipe['recipe_name']); ?></td>
                                        <td>
                                            <span class="category-badge <?php echo strtolower($recipe['recipe_category']); ?>">
                                                <?php echo htmlspecialchars($recipe['recipe_category']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $recipe['recipe_batchSize'] . ' ' . $recipe['recipe_unitOfMeasure']; ?></td>
                                        <td>
                                            <button class="view-ingredients" onclick="viewIngredients(<?php echo $recipe['recipe_id']; ?>)">
                                                View (<?php echo count($recipe_ingredients[$recipe['recipe_id']]); ?>)
                                            </button>
                                        </td>
                                        <td>
                                            <button class="view-instructions" onclick="viewInstructions(<?php echo $recipe['recipe_id']; ?>)">
                                                View Instructions
                                            </button>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($recipe['recipe_dateCreated'])); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($recipe['recipe_dateUpdated'])); ?></td>
                                        <td class="actions">
                                            <a href="edit_recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button class="action-btn delete-btn" onclick="deleteRecipe(<?php echo $recipe['recipe_id']; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Ingredients Modal -->
        <div id="ingredients-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Recipe Ingredients</h2>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="ingredients-list"></div>
                </div>
            </div>
        </div>

        <!-- Instructions Modal -->
        <div id="instructions-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Recipe Instructions</h2>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="instructions-content"></div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/dashboard.js"></script>
    <script src="js/view_recipes.js"></script>
</body>
</html> 