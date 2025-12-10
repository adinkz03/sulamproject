<?php

class FuneralAssistance {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function requestAssistance($deathId, $amount, $requestedBy, $notes = '') {
        $stmt = $this->mysqli->prepare('INSERT INTO funeral_assistance (death_id, amount, requested_by, notes, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        if (!$stmt) return false;
        $status = 'pending';
        $stmt->bind_param('idsss', $deathId, $amount, $requestedBy, $notes, $status);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? $this->mysqli->insert_id : false;
    }

    public function approve($id, $approver) {
        $stmt = $this->mysqli->prepare('UPDATE funeral_assistance SET status = "approved", approved_by = ?, approved_at = NOW() WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('si', $approver, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
