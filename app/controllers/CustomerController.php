<?php
// app/controllers/CustomerController.php

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Setting.php';

class CustomerController {

    private $appointmentModel;
    private $serviceModel;
    private $settingModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->settingModel = new Setting();
    }

    // Home page - shows booking form
    public function index() {
        $services = $this->serviceModel->getAll(true);
        $settings = $this->settingModel->getAll();
        $success = $_SESSION['booking_success'] ?? null;
        $booking_ref = $_SESSION['booking_ref'] ?? null;
        unset($_SESSION['booking_success'], $_SESSION['booking_ref']);

        require __DIR__ . '/../views/customer/home.php';
    }

    // Handle booking form submission
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/index.php');
            exit;
        }

        $errors = [];

        // Validate input
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $comm_type = $_POST['communication_type'] ?? '';
        $comm_value = trim($_POST['communication_value'] ?? '');
        $service_id = (int)($_POST['service_id'] ?? 0);
        $date = $_POST['appointment_date'] ?? '';
        $time = $_POST['appointment_time'] ?? '';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($first_name)) $errors[] = 'First name is required.';
        if (empty($last_name))  $errors[] = 'Last name is required.';
        if (!in_array($comm_type, ['telephone', 'email'])) $errors[] = 'Please select a communication method.';

        if ($comm_type === 'email' && !filter_var($comm_value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($comm_type === 'telephone' && empty($comm_value)) {
            $errors[] = 'Please enter a phone number.';
        }

        if (!$service_id) $errors[] = 'Please select a service.';
        if (empty($date))  $errors[] = 'Please select a date.';
        if (empty($time))  $errors[] = 'Please select a time slot.';

        // Check date is not in the past
        if (!empty($date) && strtotime($date) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Please select a future date.';
        }

        // Check slot is still available
        if (empty($errors)) {
            $booked = $this->appointmentModel->getBookedSlots($date);
            $service = $this->serviceModel->getById($service_id);
            if ($this->isSlotTaken($time, $booked, $service['duration'])) {
                $errors[] = 'Sorry, that time slot is no longer available. Please choose another.';
            }
        }

        if (!empty($errors)) {
            $services = $this->serviceModel->getAll(true);
            $settings = $this->settingModel->getAll();
            $form_errors = $errors;
            $form_data = $_POST;
            require __DIR__ . '/../views/customer/home.php';
            return;
        }

        // Create appointment
        $result = $this->appointmentModel->create([
            'first_name'         => $first_name,
            'last_name'          => $last_name,
            'communication_type' => $comm_type,
            'communication_value'=> $comm_value,
            'service_id'         => $service_id,
            'appointment_date'   => $date,
            'appointment_time'   => $time,
            'notes'              => $notes,
        ]);

        $_SESSION['booking_success'] = true;
        $_SESSION['booking_ref'] = $result['reference'];
        header('Location: ' . BASE_URL . '/index.php?page=confirm&ref=' . $result['reference']);
        exit;
    }

    // Booking confirmation page
    public function confirm() {
        $ref = $_GET['ref'] ?? '';
        $appointment = null;
        if ($ref) {
            $appointment = $this->appointmentModel->getByReference($ref);
        }
        $settings = $this->settingModel->getAll();
        require __DIR__ . '/../views/customer/confirm.php';
    }

    private function isSlotTaken($time, $bookedSlots, $newDuration) {
        $newStart = strtotime($time);
        $newEnd   = $newStart + ($newDuration * 60);

        foreach ($bookedSlots as $slot) {
            $slotStart = strtotime($slot['appointment_time']);
            $slotEnd   = $slotStart + ($slot['duration'] * 60);
            // Check overlap
            if ($newStart < $slotEnd && $newEnd > $slotStart) {
                return true;
            }
        }
        return false;
    }
}
