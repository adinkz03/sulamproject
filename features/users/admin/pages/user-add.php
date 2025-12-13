<?php
// Admin Add User Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();
requireAdmin();

$message = '';
$messageClass = 'notice';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    $roles = isset($_POST['roles']) ? trim($_POST['roles']) : 'user';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $marital_status = isset($_POST['marital_status']) ? trim($_POST['marital_status']) : null;
    $income_range = isset($_POST['income_range']) && $_POST['income_range'] !== '' ? trim($_POST['income_range']) : null;

    // Validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($username)) $errors[] = 'Username is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($password)) $errors[] = 'Password is required';
    if (empty($confirm_password)) $errors[] = 'Confirm password is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';

    if (empty($errors)) {
        // Check if username or email already exists
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $message = 'Username or email already exists';
            $messageClass = 'notice error';
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $mysqli->prepare('INSERT INTO users (name, username, email, password, roles, phone_number, address, marital_status, income_range, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
            
            if ($stmt) {
                $stmt->bind_param('sssssssss', $name, $username, $email, $password_hash, $roles, $phone_number, $address, $marital_status, $income_range);
                
                if ($stmt->execute()) {
                    $message = 'User created successfully';
                    $messageClass = 'notice success';
                    
                    // Redirect to user management page after 2 seconds
                    header("refresh:2;url=" . url('users'));
                } else {
                    $message = 'Failed to create user: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
                    $messageClass = 'notice error';
                }
                $stmt->close();
            } else {
                $message = 'Failed to prepare statement: ' . htmlspecialchars($mysqli->error);
                $messageClass = 'notice error';
            }
        }
    } else {
        $message = implode('<br>', $errors);
        $messageClass = 'notice error';
    }
}

// Define page header
$pageHeader = [
    'title' => 'Add New User',
    'subtitle' => 'Create a new user account for the system.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Users', 'url' => url('users')],
        ['label' => 'Add User', 'url' => null],
    ]
];

// 1. Capture the inner content
ob_start();
?>
<div class="small-card" style="max-width:800px;margin:0 auto;">
    <h2>Add New User</h2>
    <?php if ($message): ?>
        <div class="<?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="post" id="addUserForm">
        <!-- Role Selection - Priority Field -->
        <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--accent);">
            <label style="margin: 0;">
                <strong>User Role *</strong>
                <select name="roles" id="roleSelect" required style="margin-top: 0.5rem;">
                    <option value="user" <?php echo (!isset($_POST['roles']) || $_POST['roles'] === 'user') ? 'selected' : ''; ?>>Regular User (Resident)</option>
                    <option value="admin" <?php echo (isset($_POST['roles']) && $_POST['roles'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                </select>
                <small style="display: block; margin-top: 0.25rem; color: #6b7280;">
                    <span id="roleHint">Regular users are residents who can submit applications. Admins manage the system.</span>
                </small>
            </label>
        </div>

        <!-- Basic Information (Always Required) -->
        <h3 style="margin-bottom: 1rem; color: #374151; font-size: 1.1rem;">Basic Information</h3>
        
        <div class="grid-2">
            <label>Full Name *
                <input type="text" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </label>

            <label>Username *
                <input type="text" name="username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </label>
        </div>

        <div class="grid-2">
            <label>Email Address *
                <input type="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </label>
        </div>

        <div class="grid-2">
            <label>Password *
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required minlength="6">
                    <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')" 
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280;">
                        <i id="togglePasswordIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <small>Minimum 6 characters</small>
            </label>

            <label>Confirm Password *
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    <button type="button" onclick="togglePassword('confirm_password', 'toggleConfirmPasswordIcon')" 
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280;">
                        <i id="toggleConfirmPasswordIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <small>Re-enter your password</small>
            </label>
        </div>

        <!-- Personal Details (Only for Regular Users) -->
        <div id="personalDetailsSection">
            <h3 style="margin: 2rem 0 1rem; color: #374151; font-size: 1.1rem;">Personal Details</h3>
            <p style="margin-bottom: 1rem; color: #6b7280; font-size: 0.9rem;">
                <i class="fa-solid fa-info-circle"></i> Additional information for resident management
            </p>

            <div class="grid-2">
                <label>Phone Number
                    <input type="text" name="phone_number" id="phoneNumber" value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                </label>

                <label>Marital Status
                    <select name="marital_status" id="maritalStatus">
                        <option value="">Select Status</option>
                        <?php 
                        $statuses = ['single', 'married', 'divorced', 'widowed', 'others'];
                        foreach($statuses as $status): 
                            $selected = (isset($_POST['marital_status']) && $_POST['marital_status'] === $status) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo ucfirst($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <label>Address
                <textarea name="address" id="address" rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </label>

            <label>Monthly Income Range
                <select name="income_range" id="incomeRange">
                    <option value="">Select Income Range</option>
                    <option value="below_5250" <?php echo (isset($_POST['income_range']) && $_POST['income_range'] === 'below_5250') ? 'selected' : ''; ?>>
                        Below RM5,250
                    </option>
                    <option value="between_5250_11820" <?php echo (isset($_POST['income_range']) && $_POST['income_range'] === 'between_5250_11820') ? 'selected' : ''; ?>>
                        RM5,250 - RM11,820
                    </option>
                    <option value="above_11820" <?php echo (isset($_POST['income_range']) && $_POST['income_range'] === 'above_11820') ? 'selected' : ''; ?>>
                        Above RM11,820
                    </option>
                </select>
            </label>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-user-plus"></i>
                Create User
            </button>
            <a class="btn outline" href="<?php echo url('users'); ?>">
                <i class="fa-solid fa-times"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId, iconId) {
    const passwordField = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
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
        document.getElementById('incomeRange').value = '';
    } else {
        // Show personal details for regular users
        personalSection.style.display = 'block';
        roleHint.textContent = 'Regular users are residents who can submit applications. Admins manage the system.';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePersonalDetails();
    
    // Add event listener for role changes
    document.getElementById('roleSelect').addEventListener('change', togglePersonalDetails);
});
</script>
<?php
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Add New User';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
