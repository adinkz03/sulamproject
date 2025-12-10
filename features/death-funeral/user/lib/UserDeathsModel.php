<?php
/**
 * User Deaths Model
 * Database operations for user death notifications
 */

class UserDeathsModel {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    /**
     * Create a new death notification
     */
    public function create($data) {
        $stmt = $this->mysqli->prepare(
            'INSERT INTO death_notifications (deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, reported_by) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return ['success' => false, 'error' => $this->mysqli->error];
        }

        $stmt->bind_param(
            'sssssssi',
            $data['deceased_name'],
            $data['ic_number'],
            $data['date_of_death'],
            $data['place_of_death'],
            $data['cause_of_death'],
            $data['next_of_kin_name'],
            $data['next_of_kin_phone'],
            $data['reported_by']
        );

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return ['success' => true, 'id' => $id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $error];
        }
    }

    /**
     * Get notifications by user
     */
    public function getByUserId($userId) {
        $items = [];
        $stmt = $this->mysqli->prepare(
            'SELECT * FROM death_notifications WHERE reported_by = ? ORDER BY created_at DESC'
        );

        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $stmt->close();
        }

        return $items;
    }

    /**
     * Get single notification if owned by user
     */
    public function getById($id, $userId) {
        $stmt = $this->mysqli->prepare(
            'SELECT * FROM death_notifications WHERE id = ? AND reported_by = ?'
        );

        if ($stmt) {
            $stmt->bind_param('ii', $id, $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row;
        }

        return null;
    }

    /**
     * Count user notifications
     */
    public function countByUserId($userId) {
        $stmt = $this->mysqli->prepare(
            'SELECT COUNT(*) as count FROM death_notifications WHERE reported_by = ?'
        );

        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return (int) $row['count'];
        }

        return 0;
    }

    /**
     * Count verified notifications for user
     */
    public function countVerifiedByUserId($userId) {
        $stmt = $this->mysqli->prepare(
            'SELECT COUNT(*) as count FROM death_notifications WHERE reported_by = ? AND verified = 1'
        );

        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return (int) $row['count'];
        }

        return 0;
    }

    /**
     * Get funeral logistics for user's notifications
     */
    public function getFuneralLogisticsByUser($userId) {
        $items = [];
        $stmt = $this->mysqli->prepare(
            'SELECT fl.*, dn.deceased_name FROM funeral_logistics fl 
             INNER JOIN death_notifications dn ON fl.death_notification_id = dn.id 
             WHERE dn.reported_by = ? ORDER BY fl.created_at DESC'
        );

        if ($stmt) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $stmt->close();
        }

        return $items;
    }
}
?>
