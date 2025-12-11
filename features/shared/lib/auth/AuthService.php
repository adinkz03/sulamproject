<?php
/**
 * Authentication Service
 * Handles user authentication and authorization
 */

require_once __DIR__ . '/../database/Database.php';
require_once __DIR__ . '/session.php';

class AuthService {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function login($username, $password) {
        $user = $this->db->fetchOne(
            "SELECT id, username, email, password, roles FROM users WHERE username = ? OR email = ?",
            [$username, $username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['roles'] ?? 'user';
            $_SESSION['last_regeneration'] = time();
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['roles'] ?? 'user'
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    public function register($name, $username, $email, $password, $role = 'user', $phone = null, $marital_status = null, $address = null, $income_range = null) {
        // Check if username or email already exists
        $existing = $this->db->fetchOne(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existing) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Map income ranges to approximate numeric values for DB (since DB uses DECIMAL for income)
        // Adjust these representative values as needed or update DB schema to support ranges later.
        $incomeValue = null;
        if ($income_range === 'below_5250') {
            $incomeValue = 2500.00; 
        } elseif ($income_range === 'between_5250_11820') {
            $incomeValue = 8500.00;
        } elseif ($income_range === 'above_11820') {
            $incomeValue = 15000.00;
        }

        try {
            $this->db->execute(
                "INSERT INTO users (name, username, email, password, roles, phone_number, marital_status, address, income) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$name, $username, $email, $hashedPassword, $role, $phone, $marital_status, $address, $incomeValue]
            );
            
            return [
                'success' => true,
                'user_id' => $this->db->lastInsertId(),
                'message' => 'Registration successful'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    public function logout() {
        destroySession();
        return ['success' => true];
    }
    
    public function getCurrentUser() {
        if (!isAuthenticated()) {
            return null;
        }
        
        return $this->db->fetchOne(
            "SELECT id, username, email, roles, created_at FROM users WHERE id = ?",
            [getUserId()]
        );
    }
}
