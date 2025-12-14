<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
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
        $message = $result['message'] ?? '';
        $messageClass = $result['messageClass'] ?? '';
    } else {
        $result = $controller->handleCreate();
        $message = $result['message'] ?? '';
        $messageClass = $result['messageClass'] ?? '';
    }
    
    // Redirect to prevent form resubmission on page refresh
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch Data
$items = $controller->getAll();
$funeralLogistics = $controller->getFuneralLogistics();

$pageHeader = [
    'title' => 'Death & Funeral Management',
    'subtitle' => 'Manage death notifications and funeral logistics.',
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

  <script src="/features/death-funeral/admin/assets/js/admin-death-funeral.js"></script>

  <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Left Column: Record New Notification -->
    <div>
      <?php include $ROOT . '/features/death-funeral/admin/views/manage-notifications.php'; ?>
    </div>

    <!-- Right Column: Verify & Logistics -->
    <div>
      <?php include $ROOT . '/features/death-funeral/admin/views/verify-death.php'; ?>
      <div class="mt-4">
        <?php include $ROOT . '/features/death-funeral/admin/views/record-logistics.php'; ?>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <?php include $ROOT . '/features/death-funeral/admin/views/funeral-logistics.php'; ?>
  </div>

  <!-- Edit Logistics Modal -->
  <?php include $ROOT . '/features/death-funeral/admin/views/edit-logistics-modal.php'; ?>

</div>
<?php
$content = ob_get_clean();

// Wrap with app layout then base
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

$pageTitle = 'Death & Funeral Management';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
