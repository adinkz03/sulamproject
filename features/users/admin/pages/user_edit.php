<?php
// Admin Edit User Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { 
    http_response_code(400); 
    echo 'Bad Request'; 
    exit; 
}

$message = '';
$messageClass = 'notice';

// --- Define income mappings and function ---
$INCOME_RANGES = [
    'below_5250' => 5000,
    'between_5250_11820' => 10000,
    'above_11820' => 15000
];

/**
 * Convert database income value to range key
 */
function incomeToRange($income, $mappings) {
    if ($income === null) return '';
    foreach ($mappings as $range => $value) {
        if ($income <= $value) return $range;
    }
    return 'above_11820';
}

/**
 * Convert range key to database income value
 */
function rangeToIncomeValue($range, $mappings) {
    return $mappings[$range] ?? null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle user deletion
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Prevent deleting yourself
        if ($id === $_SESSION['user_id']) {
            $message = 'You cannot delete your own account';
            $messageClass = 'notice error';
        } else {
            // Delete user
            $stmt = $mysqli->prepare('DELETE FROM users WHERE id=?');
            if ($stmt) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $stmt->close();
                    // Redirect to users page after successful deletion
                    redirect('users?deleted=1');
                } else {
                    $message = 'Failed to delete user: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
                    $messageClass = 'notice error';
                }
                $stmt->close();
            } else {
                $message = 'Failed to prepare delete statement: ' . htmlspecialchars($mysqli->error);
                $messageClass = 'notice error';
            }
        }
    } else {
        // Handle user update
        $role = isset($_POST['roles']) ? trim($_POST['roles']) : 'resident';
        $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
        $address = isset($_POST['address']) ? trim($_POST['address']) : null;
        $housing_status = isset($_POST['housing_status']) ? trim($_POST['housing_status']) : null;
        $marital_status = isset($_POST['marital_status']) ? trim($_POST['marital_status']) : null;
        $income_range = isset($_POST['income_range']) && $_POST['income_range'] !== '' ? trim($_POST['income_range']) : null;
        $is_deceased = isset($_POST['is_deceased']) ? 1 : 0;

        // Convert range to income value
        $income = !empty($income_range) ? rangeToIncomeValue($income_range, $INCOME_RANGES) : null;

        // Validation
        $errors = [];
        if (!in_array($role, ['resident', 'admin'])) {
            $errors[] = 'Invalid role selected';
        }

        if (empty($errors)) {
            // For admin role, set personal details to NULL
            if ($role === 'admin') {
                $phone_number = null;
                $address = null;
                $housing_status = null;
                $marital_status = null;
                $income = null;
            }

            $stmt = $mysqli->prepare('UPDATE users SET roles=?, phone_number=?, address=?, housing_status=?, marital_status=?, income=?, is_deceased=? WHERE id=?');
            if ($stmt) {
                $stmt->bind_param('sssssdii', $role, $phone_number, $address, $housing_status, $marital_status, $income, $is_deceased, $id);
                if ($stmt->execute()) {
                    $stmt->close();
                    
                    // Handle death record if needed
                    if ($is_deceased && (isset($_POST['date']) || isset($_POST['time']) || isset($_POST['islamic_date']))) {
                        $date = isset($_POST['date']) && $_POST['date'] !== '' ? $_POST['date'] : null;
                        $time = isset($_POST['time']) && $_POST['time'] !== '' ? $_POST['time'] : null;
                        $islamic_date = isset($_POST['islamic_date']) && $_POST['islamic_date'] !== '' ? $_POST['islamic_date'] : null;
                        
                        // Check if death record already exists
                        $check_stmt = $mysqli->prepare('SELECT id FROM deaths WHERE user_id=? LIMIT 1');
                        $check_stmt->bind_param('i', $id);
                        $check_stmt->execute();
                        $exists = $check_stmt->get_result()->num_rows > 0;
                        $check_stmt->close();

                        if (!$exists) {
                            $stmt2 = $mysqli->prepare('INSERT INTO deaths (user_id, time, date, islamic_date) VALUES (?, ?, ?, ?)');
                            if ($stmt2) {
                                $stmt2->bind_param('isss', $id, $time, $date, $islamic_date);
                                $stmt2->execute();
                                $stmt2->close();
                            }
                        }
                    }
                    
                    // Redirect after successful update
                    redirect('users?updated=1');
                } else {
                    $message = 'Update failed: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
                    $messageClass = 'notice error';
                }
                $stmt->close();
            } else {
                $message = 'Failed to prepare update: ' . htmlspecialchars($mysqli->error);
                $messageClass = 'notice error';
            }
        } else {
            $message = implode('<br>', $errors);
            $messageClass = 'notice error';
        }
    }
}

// Only load user data if we're displaying the form (not redirecting)
if (!isset($_POST['action']) || $_POST['action'] !== 'delete') {
    // Load user
    $stmt = $mysqli->prepare('SELECT id, name, username, email, roles, phone_number, address, housing_status, marital_status, income, is_deceased FROM users WHERE id=? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    if (!$user) { 
        http_response_code(404); 
        echo 'User not found'; 
        exit; 
    }

    // Convert income to range for form display
    $selected_income_range = incomeToRange($user['income'], $INCOME_RANGES);
}

// Define page header
$pageHeader = [
    'title' => 'Edit User: ' . htmlspecialchars($user['name']),
    'subtitle' => 'Update user information and settings.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Users', 'url' => url('users')],
        ['label' => 'Edit', 'url' => null],
    ]
];

// 1. Capture the inner content
ob_start();
?>
<div class="small-card" style="max-width:800px;margin:0 auto;">
    <h2>Edit User: <?php echo htmlspecialchars($user['name']); ?></h2>
    <?php if ($message): ?>
        <div class="<?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="editUserForm">
        <!-- Role Selection - Priority Field -->
        <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--accent);">
            <label style="margin: 0;">
                <strong>User Role *</strong>
                <select name="roles" id="roleSelect" required style="margin-top: 0.5rem;">
                    <option value="resident" <?php echo $user['roles'] === 'resident' ? 'selected' : ''; ?>>Regular User (Resident)</option>
                    <option value="admin" <?php echo $user['roles'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280;">
                    <span id="roleHint">
                        <?php echo $user['roles'] === 'admin' ? 'Administrators manage the system and don\'t require personal details.' : 'Regular users are residents who can submit applications.'; ?>
                    </span>
                </small>
            </label>
        </div>

        <!-- Basic Information (Read-only) -->
        <h3 style="margin-bottom: 1rem; color: #374151; font-size: 1.1rem;">Basic Information</h3>
        
        <div class="grid-2">
            <label>Full Name
                <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
            </label>

            <label>Username
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </label>
        </div>

        <label>Email Address
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
        </label>

        <!-- Personal Details (Only for Regular Users) -->
        <div id="personalDetailsSection" style="<?php echo $user['roles'] === 'admin' ? 'display:none;' : ''; ?>">
            <h3 style="margin: 2rem 0 1rem; color: #374151; font-size: 1.1rem;">Personal Details</h3>
            <p style="margin-bottom: 1rem; color: #6b7280; font-size: 0.9rem;">
                <i class="fa-solid fa-info-circle"></i> Additional information for resident management
            </p>

            <div class="grid-2">
                <label>Phone Number
                    <input type="text" name="phone_number" id="phoneNumber" value="<?php echo htmlspecialchars((string)$user['phone_number']); ?>">
                </label>

                <label>Marital Status
                    <select name="marital_status" id="maritalStatus">
                        <option value="">Select Status</option>
                        <?php 
                        $statuses = ['single', 'married', 'divorced', 'widowed', 'others'];
                        foreach($statuses as $status): 
                            $selected = ($user['marital_status'] === $status) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo ucfirst($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <label>Address
                <textarea name="address" id="address" rows="3"><?php echo htmlspecialchars((string)$user['address']); ?></textarea>
            </label>

            <div class="grid-2">
                <label for="housing_status">Housing Status
                    <select id="housing_status" name="housing_status">
                        <option value="">Select Housing Status</option>
                        <option value="renting" <?php echo $user['housing_status'] === 'renting' ? 'selected' : ''; ?>>
                            Renting
                        </option>
                        <option value="own_house" <?php echo $user['housing_status'] === 'own_house' ? 'selected' : ''; ?>>
                            Own House
                        </option>
                    </select>
                </label>

                <label>Monthly Income Range
                    <select name="income_range" id="incomeRange">
                        <option value="">Select Income Range</option>
                        <option value="below_5250" <?php echo $selected_income_range === 'below_5250' ? 'selected' : ''; ?>>
                            Below RM5,250
                        </option>
                        <option value="between_5250_11820" <?php echo $selected_income_range === 'between_5250_11820' ? 'selected' : ''; ?>>
                            RM5,250 - RM11,820
                        </option>
                        <option value="above_11820" <?php echo $selected_income_range === 'above_11820' ? 'selected' : ''; ?>>
                            Above RM11,820
                        </option>
                    </select>
                </label>
            </div>

            <!-- Deceased Status -->
            <div style="margin: 2rem 0;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_deceased" id="isDeceased" <?php echo $user['is_deceased'] ? 'checked' : ''; ?>>
                    <strong>Mark as Deceased</strong>
                </label>
            </div>

            <!-- Death Record (optional) -->
            <fieldset id="deathRecordSection" style="<?php echo $user['is_deceased'] ? '' : 'display:none;'; ?>">
                <legend>Death Record (optional)</legend>
                <div class="grid-3">
                    <label>Date
                        <input type="date" name="date" id="deathDate">
                    </label>
                    <label>Time
                        <input type="time" name="time" id="deathTime">
                    </label>
                    <label>Islamic Date
                        <input type="text" name="islamic_date" id="islamicDate" placeholder="e.g., 10 Rabiulawal 1447H">
                    </label>
                </div>
            </fieldset>
        </div>

        <div class="actions" style="background: white; padding: 1rem 0; border-top: 2px solid #e5e7eb; margin-top: 2rem;">
            <button class="btn btn-primary" type="submit" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
                <i class="fa-solid fa-save"></i>
                Save Changes
            </button>
            <button class="btn btn-danger" type="button" onclick="confirmDelete()" style="font-size: 1rem; padding: 0.75rem 1.5rem; background-color: #dc2626; color: white;" <?php echo ($id === $_SESSION['user_id']) ? 'disabled title="Cannot delete your own account"' : ''; ?>>
                <i class="fa-solid fa-trash"></i>
                Remove User
            </button>
            <a class="btn outline" href="<?php echo url('users'); ?>" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
                <i class="fa-solid fa-times"></i>
                Cancel
            </a>
        </div>
    </form>

    <!-- Hidden delete form -->
    <form method="post" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
    </form>
</div>

<script>
// Confirm deletion
function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Toggle personal details section based on role
function togglePersonalDetails() {
    const roleSelect = document.getElementById('roleSelect');
    const personalSection = document.getElementById('personalDetailsSection');
    const roleHint = document.getElementById('roleHint');
    
    if (roleSelect.value === 'admin') {
        // Hide personal details for admin
        personalSection.style.display = 'none';
        roleHint.textContent = 'Administrators manage the system and don\'t require personal details.';
        
        // Clear personal detail values when switching to admin
        document.getElementById('phoneNumber').value = '';
        document.getElementById('maritalStatus').value = '';
        document.getElementById('address').value = '';
        document.getElementById('housing_status').value = '';
        document.getElementById('incomeRange').value = '';
        document.getElementById('isDeceased').checked = false;
        document.getElementById('deathRecordSection').style.display = 'none';
    } else {
        // Show personal details for regular users
        personalSection.style.display = 'block';
        roleHint.textContent = 'Regular users are residents who can submit applications.';
    }
}

// Toggle death record section based on deceased checkbox
function toggleDeathRecord() {
    const isDeceased = document.getElementById('isDeceased');
    const deathRecordSection = document.getElementById('deathRecordSection');
    
    if (isDeceased.checked) {
        deathRecordSection.style.display = 'block';
    } else {
        deathRecordSection.style.display = 'none';
        // Clear death record fields
        document.getElementById('deathDate').value = '';
        document.getElementById('deathTime').value = '';
        document.getElementById('islamicDate').value = '';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePersonalDetails();
    toggleDeathRecord();
    
    // Add event listener for role changes
    document.getElementById('roleSelect').addEventListener('change', togglePersonalDetails);
    
    // Add event listener for deceased checkbox
    document.getElementById('isDeceased').addEventListener('change', toggleDeathRecord);
});
</script>
<?php
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Edit User #' . $user['id'];
include $ROOT . '/features/shared/components/layouts/base.php';
?>