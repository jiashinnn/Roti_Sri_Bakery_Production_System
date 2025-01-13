let taskCount = 1;

function addTask() {
    const container = document.getElementById('task-assignments');
    const taskDiv = document.createElement('div');
    taskDiv.className = 'task-assignment';
    
    taskDiv.innerHTML = `
        <div class="form-group">
            <label>Baker</label>
            <select name="assignments[${taskCount}][user_id]" required>
                <option value="">Select Baker</option>
                ${document.querySelector('select[name="assignments[0][user_id]"]').innerHTML.slice(22)}
            </select>
        </div>
        <div class="form-group">
            <label>Task</label>
            <select name="assignments[${taskCount}][task]" required>
                <option value="">Select Task</option>
                <option value="Mixing">Mixing</option>
                <option value="Baking">Baking</option>
                <option value="Decorating">Decorating</option>
            </select>
        </div>
        <button type="button" class="remove-task" onclick="removeTask(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(taskDiv);
    taskCount++;

    // Show all remove buttons when there's more than one task
    const removeButtons = document.querySelectorAll('.remove-task');
    removeButtons.forEach(button => button.style.display = 'flex');
}

function removeTask(button) {
    const taskAssignments = document.querySelectorAll('.task-assignment');
    if (taskAssignments.length > 1) {
        button.closest('.task-assignment').remove();
        
        // Hide the remove button if only one task remains
        if (taskAssignments.length === 2) {
            document.querySelector('.remove-task').style.display = 'none';
        }
    }
}

// Add date-time validation
document.addEventListener('DOMContentLoaded', function() {
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');

    startTime.addEventListener('change', function() {
        endTime.min = this.value;
    });

    endTime.addEventListener('change', function() {
        if (this.value < startTime.value) {
            this.value = startTime.value;
        }
    });
});

function deleteBatch(batchId) {
    if (confirm('Are you sure you want to delete this batch? This action cannot be undone.')) {
        fetch('delete_batch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                batch_id: batchId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-batch-id="${batchId}"]`);
                if (row) {
                    row.remove();
                    
                    // Check if there are any batches left
                    const remainingRows = document.querySelectorAll('.batch-table tbody tr');
                    if (remainingRows.length === 0) {
                        const tableBody = document.querySelector('.batch-table tbody');
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="no-records">No batches found</td>
                            </tr>
                        `;
                    }
                }
                alert('Batch deleted successfully');
            } else {
                alert('Error deleting batch: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting batch');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const recipeSelect = document.getElementById('recipe_id');
    const scheduleSelect = document.getElementById('schedule_id');

    recipeSelect.addEventListener('change', function() {
        const selectedRecipeId = this.value;
        scheduleSelect.disabled = !selectedRecipeId;

        // Reset schedule select
        Array.from(scheduleSelect.options).forEach(option => {
            const recipeId = option.getAttribute('data-recipe');
            if (!selectedRecipeId || option.value === '') {
                option.style.display = '';
            } else {
                option.style.display = recipeId === selectedRecipeId ? '' : 'none';
            }
        });

        // Reset schedule selection
        scheduleSelect.value = '';

        // If no matching schedules are found, show a message
        const hasVisibleOptions = Array.from(scheduleSelect.options).some(option => 
            option.style.display === '' && option.value !== '');
        
        if (!hasVisibleOptions) {
            const noScheduleOption = new Option('No schedules available for this recipe', '');
            scheduleSelect.innerHTML = '';
            scheduleSelect.add(noScheduleOption);
        }

        // Hide batch info when recipe changes
        document.querySelector('.batch-info').style.display = 'none';
    });

    scheduleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const batchInfo = document.querySelector('.batch-info');
        
        if (this.value) {
            document.getElementById('total-batches').textContent = selectedOption.dataset.total;
            document.getElementById('assigned-batches').textContent = selectedOption.dataset.assigned;
            document.getElementById('completed-batches').textContent = selectedOption.dataset.completed;
            document.getElementById('remaining-batches').textContent = selectedOption.dataset.remaining;
            batchInfo.style.display = 'block';
        } else {
            batchInfo.style.display = 'none';
        }
    });
}); 