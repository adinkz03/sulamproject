// Build base path dynamically (works regardless of project folder)
const basePathMatch = window.location.pathname.match(/^(.*?)\/features\//);
const basePath = basePathMatch ? basePathMatch[1] : '';

/**
 * Verify a death notification
 */
function verifyNotification(id) {
    if (!confirm('Are you sure you want to verify this death notification?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    fetch(basePath + '/features/death-funeral/admin/ajax/verify-notification.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert('Notification verified successfully');
            location.href = window.location.href;
        } else {
            alert('Error: ' + (data.message || 'Failed to verify notification'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while verifying notification');
    });
}

/**
 * Delete a death notification
 */
function deleteNotification(id) {
    if (!confirm('Are you sure you want to delete this notification? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    fetch(basePath + '/features/death-funeral/admin/ajax/delete-notification.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert('Notification deleted successfully');
            location.href = window.location.href;
        } else {
            alert('Error: ' + (data.message || 'Failed to delete notification'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while deleting notification');
    });
}

/**
 * Edit funeral logistics with form modal
 */
function editLogistics(id) {
    // Fetch current data and show form
    fetch(basePath + '/features/death-funeral/admin/ajax/get-logistics.php?id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.ok && data.data) {
                document.getElementById('logisticsId').value = id;
                document.getElementById('editBurialDate').value = data.data.burial_date || '';
                document.getElementById('editBurialLocation').value = data.data.burial_location || '';
                document.getElementById('editGraveNumber').value = data.data.grave_number || '';
                document.getElementById('editNotes').value = data.data.notes || '';
                document.getElementById('editLogisticsModal').style.display = 'flex';
            } else {
                alert('Error loading logistics data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to load logistics data');
        });
}

/**
 * Close edit logistics modal
 */
function closeEditModal() {
    document.getElementById('editLogisticsModal').style.display = 'none';
    document.getElementById('editLogisticsForm').reset();
}

/**
 * Delete funeral logistics
 */
function deleteLogistics(id) {
    if (!confirm('Are you sure you want to delete this funeral logistics entry?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', id);

    fetch(basePath + '/features/death-funeral/admin/ajax/delete-logistics.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.ok) {
            alert('Logistics deleted successfully');
            location.href = window.location.href;
        } else {
            alert('Error: ' + (data.message || 'Failed to delete logistics'));
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('An error occurred while deleting logistics');
    });
}

/**
 * Submit notification form
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.death-page form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const deceasedName = form.querySelector('input[name="full_name"]').value.trim();
            const dateOfDeath = form.querySelector('input[name="date_of_death"]').value.trim();
            
            if (!deceasedName || !dateOfDeath) {
                e.preventDefault();
                alert('Please fill in all required fields (Deceased Name and Date of Death)');
                return false;
            }
        });
    }

    // Handle edit logistics form submission
    const editForm = document.getElementById('editLogisticsForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(basePath + '/features/death-funeral/admin/ajax/update-logistics.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alert('Logistics updated successfully');
                    location.href = window.location.href;
                } else {
                    alert('Error: ' + (data.message || 'Failed to update logistics'));
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('An error occurred while updating logistics');
            });
        });
    }
});
