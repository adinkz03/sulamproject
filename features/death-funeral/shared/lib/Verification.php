<?php

class Verification {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function markVerified($id, $verifier, $notes = '') {
        $stmt = $this->mysqli->prepare('UPDATE death_notifications SET verified = 1, verified_by = ?, verification_notes = ?, verified_at = NOW() WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('ssi', $verifier, $notes, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
