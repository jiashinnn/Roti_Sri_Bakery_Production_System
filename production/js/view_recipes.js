function deleteRecipe(recipeId) {
    if (confirm('Are you sure you want to delete this recipe? This action cannot be undone.')) {
        fetch('delete_recipe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                recipe_id: recipeId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-recipe-id="${recipeId}"]`);
                if (row) {
                    row.remove();
                    
                    const remainingRows = document.querySelectorAll('.recipes-table tbody tr');
                    if (remainingRows.length === 0) {
                        const tableContainer = document.querySelector('.table-responsive');
                        tableContainer.innerHTML = `
                            <div class="no-recipes">
                                <p>No recipes found.</p>
                            </div>
                        `;
                    }
                }
                alert('Recipe deleted successfully');
            } else {
                alert('Error deleting recipe: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting recipe');
        });
    }
}

function viewIngredients(recipeId) {
    fetch(`get_ingredients.php?recipe_id=${recipeId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('ingredients-modal');
            const ingredientsList = document.getElementById('ingredients-list');
            
            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>Ingredient</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            data.forEach(ingredient => {
                html += `
                    <tr>
                        <td>${ingredient.ingredient_name}</td>
                        <td>${ingredient.ingredient_quantity}</td>
                        <td>${ingredient.ingredient_unitOfMeasure}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            ingredientsList.innerHTML = html;
            modal.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading ingredients');
        });
}

// Close modal when clicking the close button or outside the modal
document.querySelector('.close-modal').addEventListener('click', () => {
    document.getElementById('ingredients-modal').style.display = 'none';
});

window.addEventListener('click', (event) => {
    const modal = document.getElementById('ingredients-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}); 