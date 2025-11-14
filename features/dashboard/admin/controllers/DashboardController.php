<?php
/**
 * Dashboard Controller
 * Handles dashboard view for admin and regular users
 */

require_once __DIR__ . '/../../../shared/controllers/BaseController.php';

class DashboardController extends BaseController {
    
    public function showAdminDashboard() {
        $this->requireAdmin();
        
        $username = $_SESSION['username'] ?? 'Admin';
        
        ob_start();
        include __DIR__ . '/../views/admin-overview.php';
        $dashboardContent = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../../../shared/components/layouts/dashboard-layout.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Dashboard';
        $additionalStyles = ['/features/dashboard/admin/assets/admin-dashboard.css'];
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
    
    public function showUserDashboard() {
        $this->requireAuth();
        
        $username = $_SESSION['username'] ?? 'User';
        
        ob_start();
        include __DIR__ . '/../../user/views/user-overview.php';
        $dashboardContent = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../../../shared/components/layouts/dashboard-layout.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Dashboard';
        $additionalStyles = ['/features/dashboard/user/assets/user-dashboard.css'];
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
}
