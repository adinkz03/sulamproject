<?php
/**
 * User Deaths Model
 * Database operations for user death notifications
 */

class UserDeathsModel
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Find a recent duplicate notification id (same name, date, reporter within given minutes)
     */
    public function findRecentDuplicateId($data, $minutes = 10)
    {
        $stmt = $this->mysqli->prepare(
            'SELECT id, created_at FROM death_notifications WHERE deceased_name = ? AND date_of_death = ? AND reported_by = ? ORDER BY created_at DESC LIMIT 1'
        );
        if (!$stmt) return false;
        $stmt->bind_param('ssi', $data['deceased_name'], $data['date_of_death'], $data['reported_by']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $created = strtotime($row['created_at']);
            if ($created !== false && (time() - $created) < ($minutes * 60)) {
                $stmt->close();
                return (int)$row['id'];
            }
        }
        $stmt->close();
        return false;
    }

    /**
     * Create a new death notification
     */
    public function create($data)
    {
        // Basic duplicate protection: avoid inserting same notification repeatedly
        // Check recent similar record (same name, date, reporter within 10 minutes)
        $dupStmt = $this->mysqli->prepare(
            'SELECT id, created_at FROM death_notifications WHERE deceased_name = ? AND date_of_death = ? AND reported_by = ? ORDER BY created_at DESC LIMIT 1'
        );
        if ($dupStmt) {
            $dupStmt->bind_param('ssi', $data['deceased_name'], $data['date_of_death'], $data['reported_by']);
            $dupStmt->execute();
            $dupRes = $dupStmt->get_result();
            if ($dupRow = $dupRes->fetch_assoc()) {
                $created = strtotime($dupRow['created_at']);
                if ($created !== false && (time() - $created) < 600) { // 10 minutes
                    $dupStmt->close();
                    return false; // treat as duplicate
                }
            }
            $dupStmt->close();
        }

        $stmt = $this->mysqli->prepare(
            'INSERT INTO death_notifications 
            (deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, reported_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

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

        return $stmt->execute();
    }

    /**
     * Get notifications by user
     */
    public function getUserNotifications($userId)
    {
        $items = [];
        $stmt = $this->mysqli->prepare(
            'SELECT id, deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, 
                    next_of_kin_name, next_of_kin_phone, verified, created_at 
             FROM death_notifications 
             WHERE reported_by = ? 
             ORDER BY created_at DESC'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $items[] = (object)$row;
        }

        return $items;
    }

    /**
     * Get verified notifications
     */
    public function getVerifiedNotifications()
    {
        $items = [];
        $stmt = $this->mysqli->prepare(
            'SELECT id, deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, 
                    next_of_kin_name, next_of_kin_phone, verified, verified_at, verified_by, created_at 
             FROM death_notifications 
             WHERE verified = 1 
             ORDER BY date_of_death DESC'
        );
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $items[] = (object)$row;
        }

        return $items;
    }

    /**
     * Get funeral logistics for user's notifications
     */
    public function getFuneralLogisticsByUser($userId)
    {
        $items = [];

        // Return logistics for notifications that are verified OR those reported by the user.
        $sql =
            'SELECT fl.id, fl.death_notification_id, fl.burial_date, fl.burial_location, 
                    fl.grave_number, fl.notes, fl.arranged_by, fl.created_at,
                    dn.deceased_name 
             FROM funeral_logistics fl
             INNER JOIN death_notifications dn ON fl.death_notification_id = dn.id
             WHERE dn.reported_by = ? OR dn.verified = 1
             ORDER BY fl.created_at DESC';

        $stmt = $this->mysqli->prepare($sql);
        if (!$stmt) {
            return $items;
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $items[] = (object) $row;
        }

        $stmt->close();
        return $items;
    }
}
?>
