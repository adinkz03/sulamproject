<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$burial_date = trim($_POST['burial_date'] ?? '');
$burial_location = trim($_POST['burial_location'] ?? '');
$grave_number = trim($_POST['grave_number'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Missing logistics ID']);
    exit;
}

$stmt = $mysqli->prepare('UPDATE funeral_logistics SET burial_date = ?, burial_location = ?, grave_number = ?, notes = ? WHERE id = ?');
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Database error']);
    exit;
}

$stmt->bind_param('ssssi', $burial_date, $burial_location, $grave_number, $notes, $id);
$success = $stmt->execute();
$stmt->close();

if ($success) {
    http_response_code(200);
    echo json_encode(['ok' => true, 'message' => 'Logistics updated successfully']);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Failed to update logistics']);
}
?>