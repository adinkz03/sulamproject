<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();

// Initialize Controller
require_once $ROOT . '/features/death-funeral/user/controllers/UserDeathsController.php';
$controller = new UserDeathsController($mysqli, $ROOT, $_SESSION['user_id'] ?? null);

// Handle Actions & flash messages (use POST-Redirect-GET to avoid duplicate submits)
$message = '';
$messageClass = '';

// Show flashed message from previous redirect
if (!empty($_SESSION['flash_message'])) {
  $message = $_SESSION['flash_message'];
  $messageClass = $_SESSION['flash_message_class'] ?? '';
  unset($_SESSION['flash_message'], $_SESSION['flash_message_class']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $result = $controller->handleCreate();

  // On success, set flash and redirect to clear POST
  if (!empty($result['success'])) {
    $_SESSION['flash_message'] = $result['message'] ?? '';
    $_SESSION['flash_message_class'] = $result['messageClass'] ?? '';
    $redirect = strtok($_SERVER['REQUEST_URI'], '?');
    header('Location: ' . $redirect);
    exit;
  }

  // Otherwise show inline error
  $message = $result['message'] ?? '';
  $messageClass = $result['messageClass'] ?? '';
}

// Fetch Data
$userItems = $controller->getUserNotifications();
$funeralLogistics = $controller->getFuneralLogistics();
$verifiedNotifications = $controller->getVerifiedNotifications();

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
    <div class="alert <?php echo htmlspecialchars($messageClass); ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <!-- Record New Death Notification -->
  <?php include $ROOT . '/features/death-funeral/user/views/record-notification.php'; ?>

  <!-- Verified Death Notifications by Admin -->
  <div class="mt-4">
    <?php include $ROOT . '/features/death-funeral/user/views/verified-notifications.php'; ?>
  </div>

  <!-- User's Submitted Notifications -->
  <div class="mt-4">
    <?php include $ROOT . '/features/death-funeral/user/views/view-notifications.php'; ?>
  </div>

  <!-- Funeral Logistics Tracking -->
  <div class="mt-4">
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
include $ROOT . '/features/shared/components/layouts/base.php';
?>
