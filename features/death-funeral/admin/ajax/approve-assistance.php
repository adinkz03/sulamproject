<?php
require_once __DIR__ . '/../../../shared/lib/database/mysqli-db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

$id = intval($_POST['id'] ?? 0);
$approver = trim($_POST['approver'] ?? '');

if ($id <= 0 || $approver === '') { echo json_encode(['ok'=>false,'error'=>'Missing required fields']); exit; }

$stmt = $mysqli->prepare('UPDATE funeral_assistance SET status = "approved", approved_by = ?, approved_at = NOW() WHERE id = ?');
if ($stmt) {
    $stmt->bind_param('si', $approver, $id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['ok'=>true]);
} else {
    echo json_encode(['ok'=>false,'error'=>$mysqli->error]);
}
