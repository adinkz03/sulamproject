<?php
/**
 * Verify Death Notification AJAX Endpoint
 */
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();
requireAdmin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid notification ID']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;

$stmt = $mysqli->prepare('UPDATE death_notifications SET verified = 1, verified_by = ?, verified_at = NOW() WHERE id = ?');

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $mysqli->error]);
    exit;
}

$stmt->bind_param('ii', $userId, $id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'message' => 'Notification verified successfully']);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>
