<?php
/**
 * Admin Deaths Model
 * Database operations for admin death notification management
 */

class AdminDeathsModel {
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
     * Get all notifications
     */
    public function getAll() {
        $items = [];
        $res = $this->mysqli->query(
            'SELECT * FROM death_notifications ORDER BY created_at DESC'
        );

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
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
        $res = $this->mysqli->query(
            'SELECT * FROM death_notifications WHERE verified = 0 ORDER BY created_at DESC'
        );

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $res->close();
        }

        return $items;
    }

    /**
     * Get verified notifications
     */
    public function getVerified() {
        $items = [];
        $res = $this->mysqli->query(
            'SELECT * FROM death_notifications WHERE verified = 1 ORDER BY verified_at DESC'
        );

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $res->close();
        }

        return $items;
    }

    /**
     * Get single notification
     */
    public function getById($id) {
        $stmt = $this->mysqli->prepare(
            'SELECT * FROM death_notifications WHERE id = ?'
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return $row;
        }

        return null;
    }

    /**
     * Verify a death notification
     */
    public function verify($id, $verifierId) {
        $stmt = $this->mysqli->prepare(
            'UPDATE death_notifications SET verified = 1, verified_by = ?, verified_at = NOW() WHERE id = ?'
        );

        if ($stmt) {
            $stmt->bind_param('ii', $verifierId, $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }

        return false;
    }

    /**
     * Update notification
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        $types = '';

        foreach ($data as $key => $value) {
            if (in_array($key, ['deceased_name', 'ic_number', 'date_of_death', 'place_of_death', 'cause_of_death', 'next_of_kin_name', 'next_of_kin_phone'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
                $types .= 's';
            }
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $types .= 'i';

        $query = 'UPDATE death_notifications SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->mysqli->prepare($query);

        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }

        return false;
    }

    /**
     * Delete notification
     */
    public function delete($id) {
        $stmt = $this->mysqli->prepare(
            'DELETE FROM death_notifications WHERE id = ?'
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }

        return false;
    }

    /**
     * Count statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total notifications
        $res = $this->mysqli->query('SELECT COUNT(*) as count FROM death_notifications');
        $stats['total'] = $res ? $res->fetch_assoc()['count'] : 0;

        // Unverified
        $res = $this->mysqli->query('SELECT COUNT(*) as count FROM death_notifications WHERE verified = 0');
        $stats['pending'] = $res ? $res->fetch_assoc()['count'] : 0;

        // Verified
        $res = $this->mysqli->query('SELECT COUNT(*) as count FROM death_notifications WHERE verified = 1');
        $stats['verified'] = $res ? $res->fetch_assoc()['count'] : 0;

        return $stats;
    }

    /**
     * Get all funeral logistics
     */
    public function getFuneralLogistics() {
        $items = [];
        $res = $this->mysqli->query(
            'SELECT fl.*, dn.deceased_name FROM funeral_logistics fl 
             LEFT JOIN death_notifications dn ON fl.death_notification_id = dn.id 
             ORDER BY fl.created_at DESC'
        );

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $res->close();
        }

        return $items;
    }

    /**
     * Get funeral logistics for a specific notification
     */
    public function getFuneralLogisticsByNotificationId($notificationId) {
        $stmt = $this->mysqli->prepare(
            'SELECT * FROM funeral_logistics WHERE death_notification_id = ? ORDER BY created_at DESC'
        );

        if ($stmt) {
            $stmt->bind_param('i', $notificationId);
            $stmt->execute();
            $res = $stmt->get_result();
            $items = [];
            while ($row = $res->fetch_assoc()) {
                $items[] = $row;
            }
            $stmt->close();
            return $items;
        }

        return [];
    }

    /**
     * Create a funeral logistics record
     * Expects array with keys: death_notification_id, burial_date, burial_location, grave_number, arranged_by, notes
     */
    public function createFuneralLogistics($data) {
        $stmt = $this->mysqli->prepare(
            'INSERT INTO funeral_logistics (death_notification_id, burial_date, burial_location, grave_number, arranged_by, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            return ['success' => false, 'error' => $this->mysqli->error];
        }

        // Bind parameters (s for date and text fields)
        $deathId = (int) ($data['death_notification_id'] ?? 0);
        $burialDate = $data['burial_date'] ?: null;
        $burialLocation = $data['burial_location'] ?? null;
        $graveNumber = $data['grave_number'] ?? null;
        $arrangedBy = (int) ($data['arranged_by'] ?? 0);
        $notes = $data['notes'] ?? null;

        $stmt->bind_param('isssis', $deathId, $burialDate, $burialLocation, $graveNumber, $arrangedBy, $notes);

        if ($stmt->execute()) {
            $id = $stmt->insert_id;
            $stmt->close();
            return ['success' => true, 'id' => $id];
        } else {
            $err = $stmt->error;
            $stmt->close();
            return ['success' => false, 'error' => $err];
        }
    }
}
?>
