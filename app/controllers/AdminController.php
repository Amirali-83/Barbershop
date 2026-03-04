<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Setting.php';

class AdminController {

    private $adminModel;
    private $appointmentModel;
    private $settingModel;

    public function __construct() {
        $this->adminModel = new Admin();
        $this->appointmentModel = new Appointment();
        $this->settingModel = new Setting();
    }

    private function requireAuth() {
        if (empty($_SESSION[ADMIN_SESSION_KEY])) {
            header('Location: ' . BASE_URL . '/index.php?page=admin-login');
            exit;
        }
        // Session timeout
        if (!empty($_SESSION['admin_last_activity'])) {
            if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
                session_destroy();
                header('Location: ' . BASE_URL . '/index.php?page=admin-login&timeout=1');
                exit;
            }
        }
        $_SESSION['admin_last_activity'] = time();
    }

    // Login page
    public function index() {
        if (!empty($_SESSION[ADMIN_SESSION_KEY])) {
            header('Location: ' . BASE_URL . '/index.php?page=admin&action=dashboard');
            exit;
        }
        $error = null;
        require __DIR__ . '/../views/admin/login.php';
    }

    // Process login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = null;

        $admin = $this->adminModel->findByUsername($username);

        if (!$admin || !$this->adminModel->verifyPassword($password, $admin['password_hash'])) {
            $error = 'Invalid username or password.';
            require __DIR__ . '/../views/admin/login.php';
            return;
        }

        $_SESSION[ADMIN_SESSION_KEY] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_last_activity'] = time();

        header('Location: ' . BASE_URL . '/index.php?page=admin&action=dashboard');
        exit;
    }

    // Logout
    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=admin-login');
        exit;
    }

    // Dashboard
    public function dashboard() {
        $this->requireAuth();
        $stats = $this->appointmentModel->getStats();
        $settings = $this->settingModel->getAll();

        // Today's appointments
        $todayAppointments = $this->appointmentModel->getAll(['date' => date('Y-m-d')]);

        // Upcoming this week
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd   = date('Y-m-d', strtotime('sunday this week'));
        $weekAppointments = $this->appointmentModel->getAll([
            'week_start' => $weekStart,
            'week_end'   => $weekEnd
        ]);

        require __DIR__ . '/../views/admin/dashboard.php';
    }
}
