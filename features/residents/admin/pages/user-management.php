<?php
// User Management Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/UsersController.php';

initSecureSession();
requireAuth();

// Instantiate Controller
$controller = new UsersController($mysqli);
$data = $controller->index();
extract($data); // Makes $users and $currentRole available to the view

// Check for success messages
$message = '';
$messageClass = 'notice success';

if (isset($_GET['updated']) && $_GET['updated'] == '1') {
    $message = 'User updated successfully!';
} elseif (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
    $message = 'User deleted successfully!';
} elseif (isset($_GET['created']) && $_GET['created'] == '1') {
    $message = 'User created successfully!';
}

// Define page header
$pageHeader = [
    'title' => 'User Management',
    'subtitle' => 'Manage system users, roles, and permissions.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Users', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Add User', 'icon' => 'fa-user-plus', 'url' => url('users/add'), 'class' => 'btn-primary'],
    ]
];

// 1. Capture the inner content
ob_start();

// Display success message if exists
if ($message): ?>
    <div class="<?php echo $messageClass; ?>" id="successMessage" style="margin-bottom: 1.5rem; position: relative; padding-right: 3rem;">
        <i class="fa-solid fa-check-circle"></i>
        <?php echo $message; ?>
        <button onclick="closeMessage()" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem; color: inherit; opacity: 0.7; transition: opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
    <script>
        function closeMessage() {
            document.getElementById('successMessage').style.display = 'none';
        }
    </script>
<?php endif;

include __DIR__ . '/../views/manage-users.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'User Management';
include $ROOT . '/features/shared/components/layouts/base.php';
?>