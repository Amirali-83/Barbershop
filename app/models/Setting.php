<?php
// app/models/Setting.php

require_once __DIR__ . '/../config/database.php';

class Setting {
    private $db;
    private static $cache = null;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll() {
        if (self::$cache === null) {
            $stmt = $this->db->prepare("SELECT setting_key, setting_value FROM settings");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            self::$cache = [];
            foreach ($rows as $row) {
                self::$cache[$row['setting_key']] = $row['setting_value'];
            }
        }
        return self::$cache;
    }

    public function get($key, $default = '') {
        $all = $this->getAll();
        return $all[$key] ?? $default;
    }

    public function set($key, $value) {
        self::$cache = null; // Bust cache
        $stmt = $this->db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        return $stmt->execute([$key, $value, $value]);
    }

    public function setMultiple($data) {
        self::$cache = null;
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return true;
    }
}
