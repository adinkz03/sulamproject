<?php

require_once __DIR__ . '/../../shared/lib/UsersModel.php';

class UsersController {
    private $model;

    public function __construct($mysqli) {
        $this->model = new UsersModel($mysqli);
    }

    public function index() {
        $role = $_GET['role'] ?? null;
        $search = $_GET['search'] ?? '';
        
        // Validate role
        if ($role && !in_array($role, ['resident', 'admin'])) {
            $role = null;
        }

        $users = $this->model->getUsers($role, $search);
        
        return [
            'users' => $users,
            'currentRole' => $role,
            'search' => $search
        ];
    }
}
