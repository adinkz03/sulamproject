<?php
// API endpoint for death notifications
$ROOT = dirname(__DIR__, 3);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';

initSecureSession();
requireAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all death notifications
    $query = 'SELECT id, deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, verified, created_at FROM death_notifications ORDER BY created_at DESC';
    $result = $mysqli->query($query);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $mysqli->error]);
        exit;
    }
    
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    $result->close();
    
    echo json_encode(['ok' => true, 'data' => $notifications]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create new death notification
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    
    $deceased_name = $input['deceased_name'] ?? '';
    $ic_number = $input['ic_number'] ?? null;
    $date_of_death = $input['date_of_death'] ?? '';
    $place_of_death = $input['place_of_death'] ?? null;
    $cause_of_death = $input['cause_of_death'] ?? null;
    $next_of_kin_name = $input['next_of_kin_name'] ?? null;
    $next_of_kin_phone = $input['next_of_kin_phone'] ?? null;
    $reported_by = $_SESSION['user_id'] ?? null;
    
    if (empty($deceased_name) || empty($date_of_death)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Deceased name and date of death are required']);
        exit;
    }
    
    $stmt = $mysqli->prepare('INSERT INTO death_notifications (deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, reported_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $mysqli->error]);
        exit;
    }
    
    $stmt->bind_param('sssssssi', $deceased_name, $ic_number, $date_of_death, $place_of_death, $cause_of_death, $next_of_kin_name, $next_of_kin_phone, $reported_by);
    
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close();
        echo json_encode(['ok' => true, 'id' => $id, 'message' => 'Death notification created successfully']);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $stmt->error]);
        $stmt->close();
        exit;
    }
}

// Unsupported method
http_response_code(405);
echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
?>
