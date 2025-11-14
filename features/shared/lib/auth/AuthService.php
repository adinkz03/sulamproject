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
            "SELECT id, username, email, password_hash, role FROM users WHERE username = ? OR email = ?",
            [$username, $username]
        );
        
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            $_SESSION['last_regeneration'] = time();
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'] ?? 'user'
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    public function register($username, $email, $password, $role = 'user') {
        // Check if username or email already exists
        $existing = $this->db->fetchOne(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );
        
        if ($existing) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $this->db->execute(
                "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)",
                [$username, $email, $hashedPassword, $role]
            );
            
            return [
                'success' => true,
                'user_id' => $this->db->lastInsertId(),
                'message' => 'Registration successful'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed'];
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
            "SELECT id, username, email, role, created_at FROM users WHERE id = ?",
            [getUserId()]
        );
    }
}
