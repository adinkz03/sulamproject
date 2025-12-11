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
        
        $initialView = 'login';
        
        ob_start();
        include __DIR__ . '/../views/login.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Login';
        $additionalStyles = [url('features/users/shared/assets/css/login.css')];
        $additionalScripts = [url('features/users/shared/assets/js/login.js')];
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
        
        $initialView = 'register';

        ob_start();
        include __DIR__ . '/../views/login.php';
        $content = ob_get_clean();
        
        $pageTitle = 'Register';
        $additionalStyles = [
            url('features/shared/assets/css/layout.css'),
            url('features/shared/assets/css/cards.css'),
            url('features/shared/assets/css/forms.css'),
            url('features/shared/assets/css/buttons.css'),
            url('features/shared/assets/css/notices.css'),
            url('features/shared/assets/css/typography.css'),
            url('features/users/shared/assets/css/login.css')
        ];
        $additionalScripts = [url('features/users/shared/assets/js/login.js')];
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
        
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone_number'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $maritalStatus = $_POST['marital_status'] ?? null;
        $address = trim($_POST['address'] ?? '');
        $incomeRange = $_POST['income_range'] ?? null;
        
        // Validation
        $errors = [];

        if (empty($name) || strlen($name) > 120) {
            $errors[] = 'Name is required and must be at most 120 characters.';
        }
        
        $usernameValidation = validateUsername($username);
        if (!$usernameValidation['valid']) {
            $errors[] = $usernameValidation['message'];
        }
        
        $emailValidation = validateEmail($email);
        if (!$emailValidation['valid']) {
            $errors[] = $emailValidation['message'];
        }

        if (!empty($phone) && strlen($phone) > 20) {
            $errors[] = 'Phone number must be at most 20 characters.';
        }

        // Basic validation for income if provided
        $validIncomeRanges = ['below_5250', 'between_5250_11820', 'above_11820'];
        if (!empty($incomeRange) && !in_array($incomeRange, $validIncomeRanges)) {
             $errors[] = 'Invalid income range selected.';
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
        
        $result = $this->authService->register($name, $username, $email, $password, 'resident', $phone, $maritalStatus, $address, $incomeRange);
        
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
