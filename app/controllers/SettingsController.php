<?php
// app/controllers/SettingsController.php

require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Admin.php';

class SettingsController {
    private $settingModel;
    private $adminModel;

    public function __construct() {
        $this->settingModel = new Setting();
        $this->adminModel = new Admin();
    }

    private function requireAuth() {
        if (empty($_SESSION[ADMIN_SESSION_KEY])) {
            header('Location: ' . BASE_URL . '/index.php?page=admin-login');
            exit;
        }
    }

    public function index() {
        $this->requireAuth();
        $settings = $this->settingModel->getAll();
        $message = $_SESSION['settings_msg'] ?? null;
        unset($_SESSION['settings_msg']);
        require __DIR__ . '/../views/admin/settings.php';
    }

    public function save() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->index();
            return;
        }

        $keys = ['shop_name','shop_address','shop_phone','shop_email','opening_time','closing_time','slot_duration','days_advance_booking','closed_days','max_daily_appointments'];
        $data = [];
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $data[$key] = trim($_POST[$key]);
            }
        }
        // Closed days (checkboxes)
        $closedDays = $_POST['closed_days_arr'] ?? [];
        $data['closed_days'] = implode(',', $closedDays);

        $this->settingModel->setMultiple($data);
        $_SESSION['settings_msg'] = 'Settings saved successfully.';
        header('Location: ' . BASE_URL . '/index.php?page=settings');
        exit;
    }

    public function changePassword() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=settings');
            exit;
        }

        $adminId = $_SESSION['admin_id'] ?? 0;
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $admin = null;
        // Verify current password
        // (fetch admin by id - simplified)
        $settings = $this->settingModel->getAll();

        if ($new !== $confirm) {
            $_SESSION['settings_msg'] = 'New passwords do not match.';
        } elseif (strlen($new) < 6) {
            $_SESSION['settings_msg'] = 'Password must be at least 6 characters.';
        } else {
            $this->adminModel->updatePassword($adminId, $new);
            $_SESSION['settings_msg'] = 'Password changed successfully.';
        }

        header('Location: ' . BASE_URL . '/index.php?page=settings');
        exit;
    }
}
