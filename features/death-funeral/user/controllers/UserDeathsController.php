<?php
/**
 * User Deaths Controller
 * Handles death notification operations for regular users
 */

require_once __DIR__ . '/../../shared/lib/DeathNotification.php';

class UserDeathsController {
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
                        $message = 'Death notification submitted successfully. Admin will verify details.';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Error submitting notification: ' . $stmt->error;
                        $messageClass = 'notice error';
                    }
                    $stmt->close();
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    /**
     * Get notifications reported by current user
     */
    public function getUserNotifications() {
        $items = [];
        $stmt = $this->mysqli->prepare('SELECT * FROM death_notifications WHERE reported_by = ? ORDER BY created_at DESC');
        
        if ($stmt) {
            $stmt->bind_param('i', $this->userId);
            $stmt->execute();
            $res = $stmt->get_result();
            
            while ($row = $res->fetch_assoc()) {
                $items[] = new DeathNotification($row);
            }
            $stmt->close();
        }
        
        return $items;
    }

    /**
     * Get single notification
     */
    public function getNotification($id) {
        $stmt = $this->mysqli->prepare('SELECT * FROM death_notifications WHERE id = ? AND reported_by = ?');
        
        if ($stmt) {
            $stmt->bind_param('ii', $id, $this->userId);
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
     * Get funeral logistics for user's notifications
     */
    public function getFuneralLogistics() {
        require_once __DIR__ . '/../lib/UserDeathsModel.php';
        $model = new UserDeathsModel($this->mysqli);
        return $model->getFuneralLogisticsByUser($this->userId);
    }
}
?>
