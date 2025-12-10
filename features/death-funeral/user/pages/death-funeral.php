<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
initSecureSession();
requireAuth();

// Initialize Controller
require_once $ROOT . '/features/death-funeral/user/controllers/UserDeathsController.php';
$controller = new UserDeathsController($mysqli, $ROOT, $_SESSION['user_id'] ?? null);

// Handle Actions
$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->handleCreate();
    $message = $result['message'];
    $messageClass = $result['messageClass'];
}

// Fetch Data
$userItems = $controller->getUserNotifications();
$controller->getFuneralLogistics();

$pageHeader = [
    'title' => 'Death & Funeral Management',
    'subtitle' => 'Report and track death notifications.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Death & Funeral', 'url' => null],
    ],
];

ob_start();
?>
<div class="death-page">

  <?php if ($message): ?>
    <div class="<?php echo $messageClass; ?>" style="margin-bottom: 1rem; padding: 1rem; border-radius: 8px; background: <?php echo strpos($messageClass, 'success') !== false ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo strpos($messageClass, 'success') !== false ? '#065f46' : '#991b1b'; ?>;">
        <?php echo $message; ?>
    </div>
  <?php endif; ?>

  <?php include $ROOT . '/features/death-funeral/user/views/record-notification.php'; ?>

  <div style="margin-top:1.5rem;">
    <?php include $ROOT . '/features/death-funeral/user/views/view-notifications.php'; ?>
  </div>

  <div style="margin-top:1.5rem;">
    <?php include $ROOT . '/features/death-funeral/user/views/logistics-tracking.php'; ?>
  </div>

</div>
<?php
$content = ob_get_clean();

// Wrap with app layout then base
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

$pageTitle = 'Death & Funeral';
$additionalStyles = [
    url('features/death-funeral/user/assets/css/user-death-funeral.css')
];
include $ROOT . '/features/shared/components/layouts/base.php';
?>
