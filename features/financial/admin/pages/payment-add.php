<?php
// Add Payment Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/FinancialController.php';

initSecureSession();
requireAuth();

// Instantiate Controller
$controller = new FinancialController($mysqli);

// Handle POST request (create)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->storePayment($_POST);
    if ($result['success']) {
        redirect('/financial/payment-account');
        exit;
    }
    // Validation failed - pass errors and old data to view
    $data = $controller->addPayment();
    $data['errors'] = $result['errors'];
    $data['old'] = $result['old'];
} else {
    $data = $controller->addPayment();
}

extract($data);

// Define page header
$pageHeader = [
    'title' => 'Add Payment',
    'subtitle' => 'Create a new payment record.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Financial', 'url' => url('financial')],
        ['label' => 'Akaun Bayaran', 'url' => url('financial/payment-account')],
        ['label' => 'Add', 'url' => null],
    ],
    'actions' => [
        [
            'label' => 'Back',
            'url' => url('financial/payment-account'),
            'icon' => 'fa-arrow-left',
            'class' => 'btn-secondary'
        ]
    ]
];

// 1. Capture the inner content
ob_start();
include __DIR__ . '/../views/payment-add.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Add Payment';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
