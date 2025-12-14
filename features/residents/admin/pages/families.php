<?php
// Families List Page
$ROOT = dirname(__DIR__, 4);

// Define APP_BASE_PATH for direct access
if (!defined('APP_BASE_PATH')) {
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $featuresPos = strpos($scriptName, '/features/');
    if ($featuresPos !== false) {
        define('APP_BASE_PATH', substr($scriptName, 0, $featuresPos));
    } else {
        define('APP_BASE_PATH', '/sulamprojectex');
    }
}

require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/FamiliesController.php';

initSecureSession();
requireAdmin();

$controller = new FamiliesController($mysqli);
$data = $controller->index();
extract($data);

$pageHeader = [
    'title' => 'Families Registry',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Residents', 'url' => url('features/residents/admin/pages/resident-management.php')],
        ['label' => 'Families', 'url' => null],
    ]
];

$additionalStyles = [
    url('features/shared/assets/css/cards.css')
];

ob_start();
include __DIR__ . '/../views/families-list.php';
$content = ob_get_clean();

ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

$pageTitle = 'Families Registry';
include $ROOT . '/features/shared/components/layouts/base.php';
