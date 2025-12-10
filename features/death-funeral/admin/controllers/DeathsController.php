<?php

class DeathsController {
    private $mysqli;
    private $rootPath;

    public function __construct($mysqli, $rootPath) {
        $this->mysqli = $mysqli;
        $this->rootPath = rtrim($rootPath, '/');
    }

    public function handleCreate() {
        $message = '';
        $messageClass = 'notice';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $deceased_name = trim($_POST['full_name'] ?? '');
            $ic_number = trim($_POST['ic_number'] ?? '');
            $date_of_death = trim($_POST['date_of_death'] ?? '');
            $place_of_death = trim($_POST['place_of_death'] ?? '');
            $cause_of_death = trim($_POST['cause_of_death'] ?? '');
            $next_of_kin_name = trim($_POST['nok_name'] ?? '');
            $next_of_kin_phone = trim($_POST['nok_phone'] ?? '');
            $reported_by = $_SESSION['user_id'] ?? null;

            if ($deceased_name === '' || $date_of_death === '') {
                $message = 'Deceased name and date of death are required.';
                $messageClass = 'notice error';
            } else {
                $stmt = $this->mysqli->prepare('INSERT INTO death_notifications (deceased_name, ic_number, date_of_death, place_of_death, cause_of_death, next_of_kin_name, next_of_kin_phone, reported_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('sssssssi', $deceased_name, $ic_number, $date_of_death, $place_of_death, $cause_of_death, $next_of_kin_name, $next_of_kin_phone, $reported_by);
                    $stmt->execute();
                    $stmt->close();
                    $message = 'Death notification recorded successfully.';
                    $messageClass = 'notice success';
                } else {
                    $message = 'Database error: ' . $this->mysqli->error;
                    $messageClass = 'notice error';
                }
            }
        }

        return ['message' => $message, 'messageClass' => $messageClass];
    }

    public function getAll() {
        $items = [];
        $res = $this->mysqli->query('SELECT * FROM death_notifications ORDER BY id DESC');
        if ($res) {
            while ($row = $res->fetch_assoc()) { $items[] = $row; }
            $res->close();
        }
        return $items;
    }
}
