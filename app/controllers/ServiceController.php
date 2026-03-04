<?php
// app/controllers/ServiceController.php

require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Setting.php';

class ServiceController {
    private $serviceModel;
    private $settingModel;

    public function __construct() {
        $this->serviceModel = new Service();
        $this->settingModel = new Setting();
    }

    private function requireAuth() {
        if (empty($_SESSION[ADMIN_SESSION_KEY])) {
            header('Location: ' . BASE_URL . '/index.php?page=admin-login');
            exit;
        }
    }

    public function index() {
        $this->requireAuth();
        $services = $this->serviceModel->getAll(false);
        $settings = $this->settingModel->getAll();
        $message = $_SESSION['service_msg'] ?? null;
        unset($_SESSION['service_msg']);
        require __DIR__ . '/../views/admin/services.php';
    }

    public function create() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $settings = $this->settingModel->getAll();
            require __DIR__ . '/../views/admin/service_form.php';
            return;
        }
        $this->serviceModel->create([
            'name'        => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'duration'    => (int)$_POST['duration'],
            'price'       => (float)$_POST['price'],
        ]);
        $_SESSION['service_msg'] = 'Service created successfully.';
        header('Location: ' . BASE_URL . '/index.php?page=services');
        exit;
    }

    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $service = $this->serviceModel->getById($id);
        if (!$service) {
            header('Location: ' . BASE_URL . '/index.php?page=services');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->serviceModel->update($id, [
                'name'        => trim($_POST['name']),
                'description' => trim($_POST['description']),
                'duration'    => (int)$_POST['duration'],
                'price'       => (float)$_POST['price'],
                'active'      => isset($_POST['active']) ? 1 : 0,
            ]);
            $_SESSION['service_msg'] = 'Service updated.';
            header('Location: ' . BASE_URL . '/index.php?page=services');
            exit;
        }
        $settings = $this->settingModel->getAll();
        require __DIR__ . '/../views/admin/service_form.php';
    }

    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        try {
            $this->serviceModel->delete($id);
            $_SESSION['service_msg'] = 'Service deleted.';
        } catch (Exception $e) {
            $_SESSION['service_msg'] = 'Cannot delete service — it has existing appointments.';
        }
        header('Location: ' . BASE_URL . '/index.php?page=services');
        exit;
    }
}
