<?php
/**
 * Routes Configuration
 * Define all application routes here
 */

require_once __DIR__ . '/features/shared/lib/utilities/Router.php';
require_once __DIR__ . '/features/users/shared/controllers/AuthController.php';
require_once __DIR__ . '/features/dashboard/admin/controllers/DashboardController.php';
require_once __DIR__ . '/features/shared/lib/auth/session.php';

$router = new Router();

// Authentication routes
$router->get('/login', function() {
    $controller = new AuthController();
    $controller->showLogin();
});

$router->post('/login', function() {
    $controller = new AuthController();
    $controller->handleLogin();
});

$router->get('/register', function() {
    $controller = new AuthController();
    $controller->showRegister();
});

$router->post('/register', function() {
    $controller = new AuthController();
    $controller->handleRegister();
});

$router->get('/logout', function() {
    $controller = new AuthController();
    $controller->logout();
});

// Dashboard routes
$router->get('/dashboard', function() {
    initSecureSession();
    requireAuth();
    
    $controller = new DashboardController();
    if (isAdmin()) {
        $controller->showAdminDashboard();
    } else {
        $controller->showUserDashboard();
    }
});

$router->get('/', function() {
    // Landing page - redirect based on auth status
    initSecureSession();
    if (isAuthenticated()) {
        header('Location: /sulamproject/dashboard');
    } else {
        header('Location: /sulamproject/login');
    }
    exit();
});

// Placeholder routes for other features
$router->get('/residents', function() {
    initSecureSession();
    requireAuth();
    
    ob_start();
    include __DIR__ . '/features/residents/admin/views/manage-residents.php';
    $dashboardContent = ob_get_clean();
    
    ob_start();
    include __DIR__ . '/features/shared/components/layouts/dashboard-layout.php';
    $content = ob_get_clean();
    
    $pageTitle = 'Residents';
    include __DIR__ . '/features/shared/components/layouts/base.php';
});

$router->get('/donations', function() {
    initSecureSession();
    requireAuth();
    
    ob_start();
    include __DIR__ . '/features/donations/admin/views/manage-donations.php';
    $dashboardContent = ob_get_clean();
    
    ob_start();
    include __DIR__ . '/features/shared/components/layouts/dashboard-layout.php';
    $content = ob_get_clean();
    
    $pageTitle = 'Donations';
    include __DIR__ . '/features/shared/components/layouts/base.php';
});

$router->get('/events', function() {
    initSecureSession();
    requireAuth();
    
    ob_start();
    include __DIR__ . '/features/events/admin/views/manage-events.php';
    $dashboardContent = ob_get_clean();
    
    ob_start();
    include __DIR__ . '/features/shared/components/layouts/dashboard-layout.php';
    $content = ob_get_clean();
    
    $pageTitle = 'Events';
    include __DIR__ . '/features/shared/components/layouts/base.php';
});

return $router;
