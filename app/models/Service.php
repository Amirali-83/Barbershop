<?php
// app/models/Service.php

require_once __DIR__ . '/../config/database.php';

class Service {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($activeOnly = true) {
        $where = $activeOnly ? 'WHERE active = 1' : '';
        $stmt = $this->db->prepare("SELECT * FROM services $where ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO services (name, description, duration, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['name'], $data['description'], $data['duration'], $data['price']]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE services SET name=?, description=?, duration=?, price=?, active=? WHERE id=?");
        return $stmt->execute([$data['name'], $data['description'], $data['duration'], $data['price'], $data['active'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
