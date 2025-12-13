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
