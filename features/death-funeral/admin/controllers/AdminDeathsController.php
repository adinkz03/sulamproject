<?php
/**
 * Admin Deaths Controller
 * Handles death notification management for admins
 */

require_once __DIR__ . '/../../shared/lib/DeathNotification.php';
require_once __DIR__ . '/../../shared/lib/Verification.php';

class AdminDeathsController {
    private $mysqli;
    private $rootPath;
    private $userId;

    public function __construct($mysqli, $rootPath, $userId = null) {
        $this->mysqli = $mysqli;
        $this->rootPath = rtrim($rootPath, '/');
        $this->userId = $userId ?? ($_SESSION['user_id'] ?? null);
    }

    /**
     * Handle creating a new death notification
     */
    public function handleCreate() {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deceased_name = trim($_POST['full_name'] ?? '');
            $ic_number = trim($_POST['ic_number'] ?? '');
            $date_of_death = trim($_POST['date_of_death'] ?? '');
            $place_of_death = trim($_POST['place_of_death'] ?? '');
            $cause_of_death = trim($_POST['cause_of_death'] ?? '');
            $next_of_kin_name = trim($_POST['nok_name'] ?? '');
            $next_of_kin_phone = trim($_POST['nok_phone'] ?? '');

            if (empty($deceased_name) || empty($date_of_death)) {
                $message = 'Deceased name and date of death are required.';
                $messageClass = 'notice error';
            } else {
                $stmt = $this->mysqli->prepare('INSERT INTO death_notifications (deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, reported_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                
                if (!$stmt) {
                    $message = 'Database error: ' . $this->mysqli->error;
                    $messageClass = 'notice error';
                } else {
                    $stmt->bind_param('sssssssi', $deceased_name, $ic_number, $date_of_death, $place_of_death, $cause_of_death, $next_of_kin_name, $next_of_kin_phone, $this->userId);
                    
                    if ($stmt->execute()) {
                        $message = 'Death notification recorded successfully.';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Error recording notification: ' . $stmt->error;
                        $messageClass = 'notice error';
                    }
                    $stmt->close();
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    /**
     * Handle creating funeral logistics (admin)
     */
    public function handleCreateLogistics() {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deathId = (int) ($_POST['death_notification_id'] ?? 0);
            $burialDate = trim($_POST['burial_date'] ?? '');
            $burialLocation = trim($_POST['burial_location'] ?? '');
            $graveNumber = trim($_POST['grave_number'] ?? '');
            $notes = trim($_POST['notes'] ?? '');

            if ($deathId <= 0) {
                $message = 'Please select a valid death notification.';
                $messageClass = 'notice error';
            } else {
                require_once __DIR__ . '/../lib/AdminDeathsModel.php';
                $model = new AdminDeathsModel($this->mysqli);
                $data = [
                    'death_notification_id' => $deathId,
                    'burial_date' => $burialDate ?: null,
                    'burial_location' => $burialLocation,
                    'grave_number' => $graveNumber,
                    'arranged_by' => $this->userId,
                    'notes' => $notes,
                ];

                $res = $model->createFuneralLogistics($data);
                if ($res['success']) {
                    $message = 'Funeral logistics recorded successfully.';
                    $messageClass = 'notice success';
                } else {
                    $message = 'Error recording logistics: ' . ($res['error'] ?? 'Unknown error');
                    $messageClass = 'notice error';
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    /**
     * Get all death notifications
     */
    public function getAll() {
        $items = [];
        $res = $this->mysqli->query('SELECT * FROM death_notifications ORDER BY created_at DESC');
        
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = new DeathNotification($row);
            }
            $res->close();
        }
        
        return $items;
    }

    /**
     * Get unverified notifications
     */
    public function getUnverified() {
        $items = [];
        $res = $this->mysqli->query('SELECT * FROM death_notifications WHERE verified = 0 ORDER BY created_at DESC');
        
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = new DeathNotification($row);
            }
            $res->close();
        }
        
        return $items;
    }

    /**
     * Verify a death notification
     */
    public function verifyNotification($id, $verifierId) {
        $stmt = $this->mysqli->prepare('UPDATE death_notifications SET verified = 1, verified_by = ?, verified_at = NOW() WHERE id = ?');
        
        if ($stmt) {
            $stmt->bind_param('ii', $verifierId, $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        
        return false;
    }

    /**
     * Get single notification
     */
    public function getNotification($id) {
        $stmt = $this->mysqli->prepare('SELECT * FROM death_notifications WHERE id = ?');
        
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($row = $res->fetch_assoc()) {
                $stmt->close();
                return new DeathNotification($row);
            }
            $stmt->close();
        }
        
        return null;
    }

    /**
     * Get all funeral logistics
     */
    public function getFuneralLogistics() {
        require_once __DIR__ . '/../lib/AdminDeathsModel.php';
        $model = new AdminDeathsModel($this->mysqli);
        return $model->getFuneralLogistics();
    }
}
?>
