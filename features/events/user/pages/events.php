<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
requireAuth();

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';

$pageHeader = [
    'title' => 'Events',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Events', 'url' => null],
    ],
];

// Fetch active events uploaded by admin
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
$events = [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $query = "SELECT id, title, description, event_date, event_time, location, image_path FROM events WHERE is_active = 1";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $query .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }

    $query .= " ORDER BY COALESCE(event_date, CURRENT_DATE) DESC, id DESC";

    $stmt = $mysqli->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $stmt->close();
    }
} catch (Throwable $e) {
    $eventsError = 'Failed to load events.';
}

// 1. Capture the inner content
ob_start();
require $ROOT . '/features/events/user/views/events.php';
$content = ob_get_clean();

// 2. Wrap into app-layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Set page title and include base layout
$pageTitle = "Events";
$additionalStyles = [
    url('features/shared/assets/css/cards.css')
];
include $ROOT . '/features/shared/components/layouts/base.php';
