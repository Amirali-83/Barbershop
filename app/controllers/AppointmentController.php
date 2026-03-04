<?php
// app/controllers/AppointmentController.php

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Setting.php';

class AppointmentController {

    private $appointmentModel;
    private $serviceModel;
    private $settingModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->settingModel = new Setting();
    }

    private function requireAuth() {
        if (empty($_SESSION[ADMIN_SESSION_KEY])) {
            header('Location: ' . BASE_URL . '/index.php?page=admin-login');
            exit;
        }
    }

    // List all appointments with filters
    public function index() {
        $this->requireAuth();

        $view = $_GET['view'] ?? 'list'; // list or calendar
        $filter = $_GET['filter'] ?? 'today';
        $filterDate = $_GET['date'] ?? date('Y-m-d');
        $filterMonth = (int)($_GET['month'] ?? date('m'));
        $filterYear  = (int)($_GET['year'] ?? date('Y'));
        $filterStatus = $_GET['status'] ?? '';

        $appointments = [];
        $calendarData = [];
        $title = '';

        if ($view === 'calendar') {
            $calendarData = $this->appointmentModel->getCalendarData($filterYear, $filterMonth);
            // Also get appointments for the selected day if provided
            if (!empty($filterDate)) {
                $appointments = $this->appointmentModel->getAll(['date' => $filterDate, 'status' => $filterStatus]);
            }
        } else {
            switch ($filter) {
                case 'today':
                    $appointments = $this->appointmentModel->getAll(['date' => date('Y-m-d'), 'status' => $filterStatus]);
                    $title = "Today's Appointments – " . date('l, d F Y');
                    break;
                case 'date':
                    $appointments = $this->appointmentModel->getAll(['date' => $filterDate, 'status' => $filterStatus]);
                    $title = 'Appointments for ' . date('l, d F Y', strtotime($filterDate));
                    break;
                case 'week':
                    $weekStart = date('Y-m-d', strtotime('monday this week'));
                    $weekEnd   = date('Y-m-d', strtotime('sunday this week'));
                    $appointments = $this->appointmentModel->getAll([
                        'week_start' => $weekStart,
                        'week_end'   => $weekEnd,
                        'status'     => $filterStatus
                    ]);
                    $title = 'This Week (' . date('d M', strtotime($weekStart)) . ' – ' . date('d M Y', strtotime($weekEnd)) . ')';
                    break;
                case 'month':
                    $appointments = $this->appointmentModel->getAll([
                        'month' => $filterMonth,
                        'year'  => $filterYear,
                        'status' => $filterStatus
                    ]);
                    $title = date('F Y', mktime(0, 0, 0, $filterMonth, 1, $filterYear));
                    break;
                default:
                    $appointments = $this->appointmentModel->getAll(['status' => $filterStatus]);
                    $title = 'All Appointments';
            }
        }

        $services = $this->serviceModel->getAll(false);
        $settings = $this->settingModel->getAll();

        require __DIR__ . '/../views/admin/appointments.php';
    }

    // Show single appointment
    public function view() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->getById($id);
        if (!$appointment) {
            header('Location: ' . BASE_URL . '/index.php?page=appointments');
            exit;
        }
        $services = $this->serviceModel->getAll(false);
        $settings = $this->settingModel->getAll();
        require __DIR__ . '/../views/admin/appointment_detail.php';
    }

    // Admin booking form
    public function index_book() {
        $this->requireAuth();
        $services = $this->serviceModel->getAll(true);
        $settings = $this->settingModel->getAll();
        $success = $_SESSION['admin_booking_success'] ?? null;
        $booking_ref = $_SESSION['admin_booking_ref'] ?? null;
        unset($_SESSION['admin_booking_success'], $_SESSION['admin_booking_ref']);
        require __DIR__ . '/../views/admin/book.php';
    }

    // Admin creates booking
    public function submit() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php?page=admin-book');
            exit;
        }

        $errors = [];
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $comm_type = $_POST['communication_type'] ?? '';
        $comm_value = trim($_POST['communication_value'] ?? '');
        $service_id = (int)($_POST['service_id'] ?? 0);
        $date = $_POST['appointment_date'] ?? '';
        $time = $_POST['appointment_time'] ?? '';
        $status = $_POST['status'] ?? 'confirmed';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($first_name)) $errors[] = 'First name is required.';
        if (empty($last_name))  $errors[] = 'Last name is required.';
        if (!$service_id)       $errors[] = 'Please select a service.';
        if (empty($date))       $errors[] = 'Please select a date.';
        if (empty($time))       $errors[] = 'Please select a time.';

        if (!empty($errors)) {
            $services = $this->serviceModel->getAll(true);
            $settings = $this->settingModel->getAll();
            $form_errors = $errors;
            $form_data = $_POST;
            require __DIR__ . '/../views/admin/book.php';
            return;
        }

        $result = $this->appointmentModel->create([
            'first_name'         => $first_name,
            'last_name'          => $last_name,
            'communication_type' => $comm_type ?: 'telephone',
            'communication_value'=> $comm_value ?: 'N/A',
            'service_id'         => $service_id,
            'appointment_date'   => $date,
            'appointment_time'   => $time,
            'notes'              => $notes,
        ]);

        // Update status if not confirmed
        if ($status !== 'confirmed') {
            $this->appointmentModel->updateStatus($result['id'], $status);
        }

        $_SESSION['admin_booking_success'] = true;
        $_SESSION['admin_booking_ref'] = $result['reference'];
        header('Location: ' . BASE_URL . '/index.php?page=admin-book&action=index_book&booked=1&ref=' . $result['reference']);
        exit;
    }

    // Update appointment status (AJAX or form)
    public function updateStatus() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!in_array($status, ['confirmed', 'pending', 'cancelled', 'completed'])) {
            $this->jsonResponse(['error' => 'Invalid status']);
            return;
        }

        $result = $this->appointmentModel->updateStatus($id, $status);
        if (isset($_POST['ajax'])) {
            $this->jsonResponse(['success' => $result]);
        } else {
            $redirect = $_POST['redirect'] ?? BASE_URL . '/index.php?page=appointments';
            header('Location: ' . $redirect);
            exit;
        }
    }

    // Edit appointment
    public function edit() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $appointment = $this->appointmentModel->getById($id);
        if (!$appointment) {
            header('Location: ' . BASE_URL . '/index.php?page=appointments');
            exit;
        }
        $services = $this->serviceModel->getAll(false);
        $settings = $this->settingModel->getAll();
        $form_data = $appointment;
        require __DIR__ . '/../views/admin/edit_appointment.php';
    }

    // Save edited appointment
    public function update() {
        $this->requireAuth();
        $id = (int)($_POST['id'] ?? 0);

        $errors = [];
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $comm_type = $_POST['communication_type'] ?? '';
        $comm_value = trim($_POST['communication_value'] ?? '');
        $service_id = (int)($_POST['service_id'] ?? 0);
        $date = $_POST['appointment_date'] ?? '';
        $time = $_POST['appointment_time'] ?? '';
        $status = $_POST['status'] ?? 'confirmed';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($first_name)) $errors[] = 'First name is required.';
        if (empty($last_name))  $errors[] = 'Last name is required.';
        if (!$service_id)       $errors[] = 'Please select a service.';
        if (empty($date))       $errors[] = 'Please select a date.';
        if (empty($time))       $errors[] = 'Please select a time.';

        if (!empty($errors)) {
            $appointment = $this->appointmentModel->getById($id);
            $services = $this->serviceModel->getAll(false);
            $settings = $this->settingModel->getAll();
            $form_errors = $errors;
            $form_data = $_POST;
            require __DIR__ . '/../views/admin/edit_appointment.php';
            return;
        }

        $this->appointmentModel->update($id, [
            'first_name' => $first_name, 'last_name' => $last_name,
            'communication_type' => $comm_type, 'communication_value' => $comm_value,
            'service_id' => $service_id, 'appointment_date' => $date,
            'appointment_time' => $time, 'status' => $status, 'notes' => $notes,
        ]);

        header('Location: ' . BASE_URL . '/index.php?page=appointments&action=view&id=' . $id . '&updated=1');
        exit;
    }

    // Delete appointment
    public function delete() {
        $this->requireAuth();
        $id = (int)($_GET['id'] ?? 0);
        $this->appointmentModel->delete($id);
        header('Location: ' . BASE_URL . '/index.php?page=appointments&deleted=1');
        exit;
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
