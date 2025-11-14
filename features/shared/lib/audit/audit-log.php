<?php
/**
 * Audit Logging
 * Records system activities for compliance and security
 */

require_once __DIR__ . '/../database/Database.php';

class AuditLog {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Log an audit event
     * 
     * @param string $action Action performed (e.g., 'login', 'create_user', 'update_resident')
     * @param string $entityType Type of entity (e.g., 'user', 'resident', 'donation')
     * @param int|null $entityId ID of the entity affected
     * @param array $details Additional details about the action
     * @param int|null $userId User who performed the action (defaults to current user)
     */
    public function log($action, $entityType, $entityId = null, $details = [], $userId = null) {
        // Table will be created via migration
        // This is a placeholder for future implementation
        
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => json_encode($details),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Log to file for now, will be database once audit_logs table is created
        $this->logToFile($logData);
        
        return true;
    }
    
    private function logToFile($data) {
        $logFile = __DIR__ . '/../../../../storage/logs/audit.log';
        $logMessage = date('Y-m-d H:i:s') . ' ' . json_encode($data) . PHP_EOL;
        error_log($logMessage, 3, $logFile);
    }
    
    public function logLogin($userId, $success = true) {
        return $this->log(
            $success ? 'login_success' : 'login_failed',
            'user',
            $userId,
            ['success' => $success]
        );
    }
    
    public function logLogout($userId) {
        return $this->log('logout', 'user', $userId);
    }
    
    public function logCreate($entityType, $entityId, $details = []) {
        return $this->log("create_$entityType", $entityType, $entityId, $details);
    }
    
    public function logUpdate($entityType, $entityId, $changes = []) {
        return $this->log("update_$entityType", $entityType, $entityId, ['changes' => $changes]);
    }
    
    public function logDelete($entityType, $entityId, $details = []) {
        return $this->log("delete_$entityType", $entityType, $entityId, $details);
    }
}
