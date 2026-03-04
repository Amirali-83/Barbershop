<?php
// app/models/Appointment.php

require_once __DIR__ . '/../config/database.php';

class Appointment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Generate unique booking reference
    private function generateReference() {
        do {
            $ref = 'BK' . strtoupper(substr(md5(uniqid()), 0, 8));
            $stmt = $this->db->prepare("SELECT id FROM appointments WHERE booking_reference = ?");
            $stmt->execute([$ref]);
        } while ($stmt->fetch());
        return $ref;
    }

    // Create new appointment
    public function create($data) {
        $ref = $this->generateReference();
        $stmt = $this->db->prepare("
            INSERT INTO appointments 
            (first_name, last_name, communication_type, communication_value, service_id, appointment_date, appointment_time, status, notes, booking_reference)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed', ?, ?)
        ");
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['communication_type'],
            $data['communication_value'],
            $data['service_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['notes'] ?? '',
            $ref
        ]);
        return ['id' => $this->db->lastInsertId(), 'reference' => $ref];
    }

    // Get all appointments (with optional filters)
    public function getAll($filters = []) {
        $where = [];
        $params = [];

        if (!empty($filters['date'])) {
            $where[] = "a.appointment_date = ?";
            $params[] = $filters['date'];
        }
        if (!empty($filters['week_start']) && !empty($filters['week_end'])) {
            $where[] = "a.appointment_date BETWEEN ? AND ?";
            $params[] = $filters['week_start'];
            $params[] = $filters['week_end'];
        }
        if (!empty($filters['month']) && !empty($filters['year'])) {
            $where[] = "MONTH(a.appointment_date) = ? AND YEAR(a.appointment_date) = ?";
            $params[] = $filters['month'];
            $params[] = $filters['year'];
        }
        if (!empty($filters['status'])) {
            $where[] = "a.status = ?";
            $params[] = $filters['status'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT a.*, s.name as service_name, s.duration, s.price
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            $whereClause
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get single appointment by ID
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, s.name as service_name, s.duration, s.price
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Get appointment by reference
    public function getByReference($ref) {
        $stmt = $this->db->prepare("
            SELECT a.*, s.name as service_name, s.duration, s.price
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.booking_reference = ?
        ");
        $stmt->execute([$ref]);
        return $stmt->fetch();
    }

    // Update appointment status
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Update appointment details (admin)
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE appointments SET
                first_name = ?, last_name = ?, communication_type = ?,
                communication_value = ?, service_id = ?, appointment_date = ?,
                appointment_time = ?, status = ?, notes = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['first_name'], $data['last_name'],
            $data['communication_type'], $data['communication_value'],
            $data['service_id'], $data['appointment_date'],
            $data['appointment_time'], $data['status'],
            $data['notes'] ?? '', $id
        ]);
    }

    // Delete appointment
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM appointments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Get booked time slots for a specific date
    public function getBookedSlots($date, $excludeId = null) {
        $sql = "SELECT appointment_time, s.duration 
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.appointment_date = ? AND a.status != 'cancelled'";
        $params = [$date];
        if ($excludeId) {
            $sql .= " AND a.id != ?";
            $params[] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Stats for dashboard
    public function getStats() {
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $stats = [];

        // Today
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date = ? AND status != 'cancelled'");
        $stmt->execute([$today]);
        $stats['today'] = $stmt->fetch()['cnt'];

        // This week
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date BETWEEN ? AND ? AND status != 'cancelled'");
        $stmt->execute([$weekStart, $weekEnd]);
        $stats['week'] = $stmt->fetch()['cnt'];

        // This month
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date BETWEEN ? AND ? AND status != 'cancelled'");
        $stmt->execute([$monthStart, $monthEnd]);
        $stats['month'] = $stmt->fetch()['cnt'];

        // Revenue this month
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(s.price), 0) as revenue
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.appointment_date BETWEEN ? AND ? AND a.status = 'completed'
        ");
        $stmt->execute([$monthStart, $monthEnd]);
        $stats['revenue'] = $stmt->fetch()['revenue'];

        // Upcoming today
        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE appointment_date = ? AND appointment_time >= ? AND status = 'confirmed'");
        $stmt->execute([$today, date('H:i:s')]);
        $stats['upcoming_today'] = $stmt->fetch()['cnt'];

        return $stats;
    }

    // Get appointments for calendar (returns array keyed by date)
    public function getCalendarData($year, $month) {
        $stmt = $this->db->prepare("
            SELECT appointment_date, COUNT(*) as count,
                   SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM appointments
            WHERE YEAR(appointment_date) = ? AND MONTH(appointment_date) = ?
            GROUP BY appointment_date
        ");
        $stmt->execute([$year, $month]);
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row) {
            $data[$row['appointment_date']] = $row;
        }
        return $data;
    }
}
