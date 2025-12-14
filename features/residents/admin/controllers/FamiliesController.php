<?php

require_once __DIR__ . '/../../shared/lib/UsersModel.php';

class FamiliesController {
    private $model;

    public function __construct($mysqli) {
        $this->model = new UsersModel($mysqli);
    }

    public function index() {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $families = $this->model->getFamilies($search);
        return ['families' => $families, 'search' => $search];
    }
}
