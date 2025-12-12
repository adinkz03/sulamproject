<?php
// Edit Deposit Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/FinancialController.php';

initSecureSession();
requireAuth();
requireAdmin();

// Get ID from query string
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    redirect('/financial/deposit-account');
    exit;
}

// Instantiate Controller
$controller = new FinancialController($mysqli);

// Handle POST request (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->updateDeposit($id, $_POST);
    if ($result['success']) {
        redirect('/financial/deposit-account');
        exit;
    }
    // Validation failed - pass errors and old data to view
    $data = $controller->editDeposit($id);
    $data['errors'] = $result['errors'];
    $data['old'] = $result['old'];
} else {
    $data = $controller->editDeposit($id);
}

// Check if record exists
if (empty($data['record'])) {
    redirect('/financial/deposit-account');
    exit;
}

extract($data);

// Define page header
$pageHeader = [
    'title' => 'Edit Deposit',
    'subtitle' => 'Modify an existing deposit record.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Financial', 'url' => url('financial')],
        ['label' => 'Akaun Terimaan', 'url' => url('financial/deposit-account')],
        ['label' => 'Edit', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Back', 'icon' => 'fa-arrow-left', 'url' => url('financial/deposit-account'), 'class' => 'btn-secondary'],
    ]
];

// 1. Capture the inner content (reuse the add form view)
ob_start();
include __DIR__ . '/../views/deposit-add.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Edit Deposit';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
