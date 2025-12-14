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
    private $model;

    public function __construct($mysqli, $rootPath, $userId = null) {
        $this->mysqli = $mysqli;
        $this->rootPath = rtrim($rootPath, '/');
        $this->userId = $userId ?? ($_SESSION['user_id'] ?? null);
        require_once $rootPath . '/features/death-funeral/user/lib/UserDeathsModel.php';
        $this->model = new UserDeathsModel($mysqli);
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
                return ['message' => $message, 'messageClass' => $messageClass, 'success' => false];
            } else {
                $data = [
                    'deceased_name' => $deceased_name,
                    'ic_number' => $ic_number,
                    'date_of_death' => $date_of_death,
                    'place_of_death' => $place_of_death,
                    'cause_of_death' => $cause_of_death,
                    'next_of_kin_name' => $next_of_kin_name,
                    'next_of_kin_phone' => $next_of_kin_phone,
                    'reported_by' => $this->userId
                ];

                $result = $this->model->create($data);

                if ($result) {
                    $message = 'Death notification submitted successfully. Admin will verify details.';
                    $messageClass = 'notice success';
                    return ['message' => $message, 'messageClass' => $messageClass, 'success' => true];
                } else {
                    // Check if this was a recent duplicate; if so, treat as success without inserting again
                    $dupId = $this->model->findRecentDuplicateId($data, 10);
                    if ($dupId) {
                        $message = 'Duplicate notification detected — already submitted.';
                        $messageClass = 'notice success';
                        return ['message' => $message, 'messageClass' => $messageClass, 'success' => true];
                    }

                    $message = 'Error submitting notification. Please try again.';
                    $messageClass = 'notice error';
                    return ['message' => $message, 'messageClass' => $messageClass, 'success' => false];
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass, 'success' => false];
    }

    /**
     * Get notifications reported by current user
     */
    public function getUserNotifications() {
        return $this->model->getUserNotifications($this->userId);
    }

    /**
     * Get funeral logistics for user's notifications
     */
    public function getFuneralLogistics() {
        return $this->model->getFuneralLogisticsByUser($this->userId);
    }

    /**
     * Get all verified notifications
     */
    public function getVerifiedNotifications() {
        return $this->model->getVerifiedNotifications();
    }
}
?>
