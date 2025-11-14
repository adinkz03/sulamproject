<?php
/**
 * Base Controller
 * Common controller functionality and middleware
 */

require_once __DIR__ . '/../lib/auth/session.php';
require_once __DIR__ . '/../lib/utilities/functions.php';

class BaseController {
    protected $db;
    protected $currentUser;
    
    public function __construct() {
        initSecureSession();
        
        if (isAuthenticated()) {
            $this->currentUser = [
                'id' => getUserId(),
                'role' => getUserRole()
            ];
        }
    }
    
    protected function requireAuth() {
        if (!isAuthenticated()) {
            $this->redirectToLogin();
        }
    }
    
    protected function requireAdmin() {
        $this->requireAuth();
        if (!isAdmin()) {
            $this->forbidden();
        }
    }
    
    protected function redirectToLogin() {
        redirect('/sulamproject/login');
    }
    
    protected function forbidden() {
        http_response_code(403);
        die('Access denied. Insufficient privileges.');
    }
    
    protected function notFound() {
        http_response_code(404);
        die('Page not found.');
    }
    
    protected function json($data, $statusCode = 200) {
        jsonResponse($data, $statusCode);
    }
    
    protected function renderView($viewPath, $data = []) {
        extract($data);
        require $viewPath;
    }
}
