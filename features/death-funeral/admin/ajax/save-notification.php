<?php
require_once __DIR__ . '/../../../shared/lib/database/mysqli-db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

$full_name = trim($_POST['full_name'] ?? '');
$dob = trim($_POST['dob'] ?? null);
$date_of_death = trim($_POST['date_of_death'] ?? null);
$place = trim($_POST['place_of_death'] ?? null);
$nok_name = trim($_POST['nok_name'] ?? null);
$nok_phone = trim($_POST['nok_phone'] ?? null);
$notes = trim($_POST['notes'] ?? null);

if ($full_name === '' || $date_of_death === '') {
    echo json_encode(['ok'=>false,'error'=>'Full name and date of death required']);
    exit;
}

$stmt = $mysqli->prepare('INSERT INTO death_notifications (full_name, dob, date_of_death, place_of_death, nok_name, nok_phone, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
if ($stmt) {
    $stmt->bind_param('sssssss', $full_name, $dob, $date_of_death, $place, $nok_name, $nok_phone, $notes);
    $stmt->execute();
    $id = $mysqli->insert_id;
    $stmt->close();
    echo json_encode(['ok'=>true,'id'=>$id]);
} else {
    echo json_encode(['ok'=>false,'error'=>$mysqli->error]);
}
