function addIngredient() {
    const container = document.getElementById('ingredients-container');
    const newRow = document.createElement('div');
    newRow.className = 'ingredient-row';
    newRow.innerHTML = `
        <div class="form-group">
            <label>Ingredient Name</label>
            <input type="text" name="ingredient_name[]" required>
        </div>
        <div class="form-group">
            <label>Quantity</label>
            <input type="number" name="ingredient_quantity[]" step="0.01" required>
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
    `;
    container.appendChild(newRow);
}

function removeIngredient(button) {
    const row = button.parentElement;
    if (document.querySelectorAll('.ingredient-row').length > 1) {
        row.remove();
    }
} 
