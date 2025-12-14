<?php

class EventsController {
    private $mysqli;
    private $uploadDir;

    public function __construct($mysqli, $rootPath) {
        $this->mysqli = $mysqli;
        $this->uploadDir = $rootPath . '/assets/uploads';
    }

    public function handleCreate() {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
            $time = !empty($_POST['event_time']) ? $_POST['event_time'] : null;
            $location = trim($_POST['location'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            $gamba = null;
            // Handle file upload if provided
            if (!empty($_FILES['gamba']['name'])) {
                if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
                $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
                $basename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
                $target = $this->uploadDir . '/' . $basename;
                if (move_uploaded_file($_FILES['gamba']['tmp_name'], $target)) {
                    $gamba = 'assets/uploads/' . $basename;
                }
            } else if (isset($_POST['gamba_url']) && $_POST['gamba_url'] !== '') {
                $gamba = trim($_POST['gamba_url']);
            }
            
            if ($title === '') { 
                $message = 'Title is required.'; 
                $messageClass = 'notice error';
            } else {
                $stmt = $this->mysqli->prepare('INSERT INTO events (title, description, event_date, event_time, location, image_path, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
                if ($stmt) { 
                    $stmt->bind_param('ssssssi', $title, $desc, $date, $time, $location, $gamba, $isActive); 
                    $stmt->execute(); 
                    $stmt->close(); 
                    $message = 'Event created successfully'; 
                    $messageClass = 'notice success'; 
                } else {
                    $message = 'Database error: ' . $this->mysqli->error;
                    $messageClass = 'notice error';
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function getAllEvents($search = '', $status = '') {
        $items = [];
        $sql = "SELECT id, title, description, event_date, event_time, location, image_path, is_active, created_at FROM events WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }

        if ($status !== '') {
            $sql .= " AND is_active = ?";
            $params[] = (int)$status;
            $types .= "i";
        }

        $sql .= " ORDER BY event_date DESC, id DESC";

        $stmt = $this->mysqli->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) { 
                $items[] = $row; 
            } 
            $stmt->close(); 
        }
        return $items;
    }

    public function handleUpdate($id) {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
            $time = !empty($_POST['event_time']) ? $_POST['event_time'] : null;
            $location = trim($_POST['location'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $gamba = null;

            if (!empty($_FILES['gamba']['name'])) {
                if (!is_dir($this->uploadDir)) { @mkdir($this->uploadDir, 0777, true); }
                $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
                $basename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
                $target = $this->uploadDir . '/' . $basename;
                if (move_uploaded_file($_FILES['gamba']['tmp_name'], $target)) {
                    $gamba = 'assets/uploads/' . $basename;
                }
            } else if (isset($_POST['gamba_url']) && $_POST['gamba_url'] !== '') {
                $gamba = trim($_POST['gamba_url']);
            }

            if ($title === '') {
                $message = 'Title is required.';
                $messageClass = 'notice error';
            } else {
                if ($gamba !== null) {
                    $stmt = $this->mysqli->prepare('UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, image_path=?, is_active=? WHERE id=?');
                    if ($stmt) {
                        $stmt->bind_param('ssssssii', $title, $desc, $date, $time, $location, $gamba, $isActive, $id);
                        $stmt->execute();
                        $stmt->close();
                        $message = 'Event updated successfully';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Database error: ' . $this->mysqli->error;
                        $messageClass = 'notice error';
                    }
                } else {
                    $stmt = $this->mysqli->prepare('UPDATE events SET title=?, description=?, event_date=?, event_time=?, location=?, is_active=? WHERE id=?');
                    if ($stmt) {
                        $stmt->bind_param('ssssssi', $title, $desc, $date, $time, $location, $isActive, $id);
                        $stmt->execute();
                        $stmt->close();
                        $message = 'Event updated successfully';
                        $messageClass = 'notice success';
                    } else {
                        $message = 'Database error: ' . $this->mysqli->error;
                        $messageClass = 'notice error';
                    }
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function handleDelete($id) {
        $message = '';
        $messageClass = 'notice';
        $stmt = $this->mysqli->prepare('DELETE FROM events WHERE id=?');
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $message = 'Event deleted.';
            $messageClass = 'notice success';
        } else {
            $message = 'Database error: ' . $this->mysqli->error;
            $messageClass = 'notice error';
        }
        return ['message' => $message, 'messageClass' => $messageClass];
    }
}
