<?php
/**
 * Authentication Controller
 * Handles login, registration, and logout
 */

require_once __DIR__ . '/../../../shared/lib/auth/AuthService.php';
require_once __DIR__ . '/../../../shared/lib/utilities/csrf.php';
require_once __DIR__ . '/../../../shared/lib/utilities/validation.php';
require_once __DIR__ . '/../../../shared/lib/utilities/functions.php';
require_once __DIR__ . '/../../../shared/lib/audit/audit-log.php';

class AuthController {
    private $authService;
    private $auditLog;
    
    public function __construct() {
        $this->authService = new AuthService();
        $this->auditLog = new AuditLog();
    }
    
    public function showLogin() {
        initSecureSession();
        
        // Redirect if already logged in
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include __DIR__ . '/../views/login.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Login';
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
    
    public function handleLogin() {
        initSecureSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
        }
        
        // Verify CSRF token
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['message'] = 'Invalid request. Please try again.';
            redirect('/login');
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['message'] = 'Please enter your username and password.';
            redirect('/login');
        }
        
        $result = $this->authService->login($username, $password);
        
        if ($result['success']) {
            $this->auditLog->logLogin($result['user']['id'], true);
            redirect('/dashboard');
        } else {
            $this->auditLog->logLogin(null, false);
            $_SESSION['message'] = $result['message'];
            redirect('/login');
        }
    }
    
    public function showRegister() {
        initSecureSession();
        
        // Redirect if already logged in
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $message = $_SESSION['message'] ?? '';
        unset($_SESSION['message']);
        
        $csrfToken = generateCsrfToken();
        
        ob_start();
        include __DIR__ . '/../views/register.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Register';
        include __DIR__ . '/../../../shared/components/layouts/base.php';
    }
    
    public function handleRegister() {
        initSecureSession();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
        }
        
        // Verify CSRF token
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['message'] = 'Invalid request. Please try again.';
            redirect('/register');
        }
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        $errors = [];
        
        $usernameValidation = validateUsername($username);
        if (!$usernameValidation['valid']) {
            $errors[] = $usernameValidation['message'];
        }
        
        $emailValidation = validateEmail($email);
        if (!$emailValidation['valid']) {
            $errors[] = $emailValidation['message'];
        }
        
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $errors[] = $passwordValidation['message'];
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
        
        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            redirect('/register');
        }
        
        $result = $this->authService->register($username, $email, $password);
        
        if ($result['success']) {
            $this->auditLog->logCreate('user', $result['user_id']);
            $_SESSION['message'] = 'Registration successful! Please login.';
            redirect('/login');
        } else {
            $_SESSION['message'] = $result['message'];
            redirect('/register');
        }
    }
    
    public function logout() {
        initSecureSession();
        
        if (isAuthenticated()) {
            $userId = getUserId();
            $this->auditLog->logLogout($userId);
        }
        
        $this->authService->logout();
        redirect('/login');
    }
}
