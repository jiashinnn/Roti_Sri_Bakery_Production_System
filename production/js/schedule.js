function deleteSchedule(scheduleId) {
    if (confirm('Are you sure you want to delete this schedule? This action cannot be undone.')) {
        fetch('delete_schedule.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                schedule_id: scheduleId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-schedule-id="${scheduleId}"]`);
                if (row) {
                    row.remove();
                    
                    // Check if there are any schedules left
                    const remainingRows = document.querySelectorAll('.schedule-table tbody tr');
                    if (remainingRows.length === 0) {
                        // Show "no schedules" message
                        const tableBody = document.querySelector('.schedule-table tbody');
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="no-records">No schedules found</td>
                            </tr>
                        `;
                    }
                }
                alert('Schedule deleted successfully');
            } else {
                alert('Error deleting schedule: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting schedule');
        });
    }
} 