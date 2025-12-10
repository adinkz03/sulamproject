<?php

class FuneralLogistics {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    public function createLogistics($id, $data) {
        $stmt = $this->mysqli->prepare('INSERT INTO funeral_logistics (death_id, location, service_time, transport, notes, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        if (!$stmt) return false;
        $stmt->bind_param('issss', $id, $data['location'], $data['service_time'], $data['transport'], $data['notes']);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? $this->mysqli->insert_id : false;
    }

    public function listForDeath($id) {
        $rows = [];
        $stmt = $this->mysqli->prepare('SELECT * FROM funeral_logistics WHERE death_id = ? ORDER BY id DESC');
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($r = $res->fetch_assoc()) { $rows[] = $r; }
            $stmt->close();
        }
        return $rows;
    }
}
