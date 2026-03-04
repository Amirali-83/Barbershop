<?php
// app/controllers/ApiController.php
// Handles AJAX requests for available time slots etc.

require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/Setting.php';

class ApiController {

    private $appointmentModel;
    private $serviceModel;
    private $settingModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->settingModel = new Setting();
    }

    // Get available time slots for a given date and service
    public function slots() {
        header('Content-Type: application/json');

        $date = $_GET['date'] ?? '';
        $serviceId = (int)($_GET['service_id'] ?? 0);

        if (!$date || !$serviceId) {
            echo json_encode(['error' => 'Missing parameters']);
            exit;
        }

        // Check it's not a past date
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            echo json_encode(['slots' => [], 'message' => 'Cannot book past dates']);
            exit;
        }

        $settings = $this->settingModel->getAll();
        $openTime   = $settings['opening_time'] ?? '09:00';
        $closeTime  = $settings['closing_time'] ?? '19:00';
        $slotMins   = (int)($settings['slot_duration'] ?? 30);
        $closedDays = explode(',', $settings['closed_days'] ?? '0');
        $closedDays = array_map('trim', $closedDays);

        // Check if day is closed (0=Sunday, 1=Monday ... 6=Saturday)
        $dayOfWeek = date('w', strtotime($date));
        if (in_array((string)$dayOfWeek, $closedDays)) {
            echo json_encode(['slots' => [], 'message' => 'Shop is closed on this day']);
            exit;
        }

        $service = $this->serviceModel->getById($serviceId);
        if (!$service) {
            echo json_encode(['error' => 'Service not found']);
            exit;
        }

        $bookedSlots = $this->appointmentModel->getBookedSlots($date);

        // Generate all possible slots
        $allSlots = [];
        $current = strtotime($date . ' ' . $openTime);
        $close   = strtotime($date . ' ' . $closeTime);
        $now     = time();

        while ($current + ($service['duration'] * 60) <= $close) {
            $slotTime = date('H:i', $current);
            $slotEnd  = date('H:i', $current + ($service['duration'] * 60));

            // Skip past slots for today
            if ($date === date('Y-m-d') && $current <= $now) {
                $current += $slotMins * 60;
                continue;
            }

            // Check if this slot overlaps with any booked slot
            $taken = false;
            foreach ($bookedSlots as $booked) {
                $bookedStart = strtotime($date . ' ' . $booked['appointment_time']);
                $bookedEnd   = $bookedStart + ($booked['duration'] * 60);
                $slotStart   = $current;
                $slotFinish  = $current + ($service['duration'] * 60);

                if ($slotStart < $bookedEnd && $slotFinish > $bookedStart) {
                    $taken = true;
                    break;
                }
            }

            $allSlots[] = [
                'time'      => $slotTime,
                'time_end'  => $slotEnd,
                'label'     => date('g:i A', $current) . ' – ' . date('g:i A', $current + ($service['duration'] * 60)),
                'available' => !$taken,
            ];

            $current += $slotMins * 60;
        }

        echo json_encode(['slots' => $allSlots, 'service' => $service]);
        exit;
    }

    // Check if shop is open on a given date
    public function checkDate() {
        header('Content-Type: application/json');
        $date = $_GET['date'] ?? '';
        if (!$date) {
            echo json_encode(['open' => false]);
            exit;
        }
        $settings = $this->settingModel->getAll();
        $closedDays = explode(',', $settings['closed_days'] ?? '0');
        $closedDays = array_map('trim', $closedDays);
        $dayOfWeek = date('w', strtotime($date));
        echo json_encode(['open' => !in_array((string)$dayOfWeek, $closedDays)]);
        exit;
    }
}
