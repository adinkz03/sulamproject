// Build base path dynamically (works regardless of project folder)
const basePathMatch = window.location.pathname.match(/^(.*?)\/features\//);
const basePath = basePathMatch ? basePathMatch[1] : '';

/**
 * Verify a death notification
 */
function verifyNotification(id) {
    if (!confirm('Are you sure you want to verify this death notification?')) {
        return false;
    }

    fetch(basePath + '/features/death-funeral/admin/ajax/verify-notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            alert('Notification verified successfully');
            location.reload();
        } else {
            alert('Error verifying notification: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while verifying the notification');
    });
}

/**
 * Delete a death notification
 */
function deleteNotification(id) {
    if (!confirm('Are you sure you want to delete this death notification? This action cannot be undone.')) {
        return false;
    }

    fetch(basePath + '/features/death-funeral/admin/ajax/delete-notification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            alert('Notification deleted successfully');
            location.reload();
        } else {
            alert('Error deleting notification: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the notification');
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
});
