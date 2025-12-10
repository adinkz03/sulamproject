<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
initSecureSession();
requireAuth();
requireAdmin();

// Initialize Controller
require_once $ROOT . '/features/death-funeral/admin/controllers/AdminDeathsController.php';
$controller = new AdminDeathsController($mysqli, $ROOT, $_SESSION['user_id'] ?? null);

// Handle Actions
$message = '';
$messageClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'record_logistics') {
    $result = $controller->handleCreateLogistics();
    $message = $result['message'];
    $messageClass = $result['messageClass'];
  } else {
    $result = $controller->handleCreate();
    $message = $result['message'];
    $messageClass = $result['messageClass'];
  }
}

// Fetch Data
$items = $controller->getAll();
$funeralLogistics = $controller->getFuneralLogistics();
$stats = [];

$pageHeader = [
    'title' => 'Death & Funeral Management',
    'subtitle' => 'Manage notifications, verification and funeral logistics.',
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

  <?php include $ROOT . '/features/death-funeral/admin/views/manage-notifications.php'; ?>

  <div style="margin-top:1.5rem;">
    <?php include $ROOT . '/features/death-funeral/admin/views/verify-death.php'; ?>
  </div>

  <div style="margin-top:1.5rem; display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem;">
    <div>
      <?php include $ROOT . '/features/death-funeral/admin/views/record-logistics.php'; ?>
    </div>
    <div>
      <?php include $ROOT . '/features/death-funeral/admin/views/funeral-logistics.php'; ?>
    </div>
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
    url('features/death-funeral/admin/assets/css/admin-death-funeral.css')
];
$additionalScripts = [
    url('features/death-funeral/admin/assets/js/admin-death-funeral.js')
];
include $ROOT . '/features/shared/components/layouts/base.php';
?>
