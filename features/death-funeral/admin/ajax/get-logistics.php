<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();
requireAdmin();

header('Content-Type: application/json');

$id = (int) ($_GET['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Missing logistics ID']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, burial_date, burial_location, grave_number, notes FROM funeral_logistics WHERE id = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Database error']);
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if ($data) {
    http_response_code(200);
    echo json_encode(['ok' => true, 'data' => $data]);
} else {
    http_response_code(404);
    echo json_encode(['ok' => false, 'message' => 'Logistics not found']);
}
?>
