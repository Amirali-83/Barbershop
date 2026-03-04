-- BarberShop Database Schema
-- Run this in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS barbershop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barbershop;

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL COMMENT 'Duration in minutes',
    price DECIMAL(10,2) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    communication_type ENUM('telephone', 'email') NOT NULL,
    communication_value VARCHAR(255) NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    notes TEXT,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Business settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default services
INSERT INTO services (name, description, duration, price) VALUES
('Haircut', 'Classic haircut styled to your preference', 30, 25.00),
('Cut & Beard Shave', 'Haircut combined with a clean beard shave and hot towel', 60, 45.00),
('Beard Trim & Shape', 'Precise beard trimming and shaping', 20, 20.00),
('Hair Wash & Style', 'Relaxing hair wash with professional styling', 45, 35.00),
('Full Grooming Package', 'Complete package: haircut, beard, wash and style', 90, 65.00),
('Kids Haircut', 'Haircut for children under 12', 20, 15.00),
('Head Shave', 'Clean head shave with hot towel finish', 30, 20.00);

-- Insert default admin user (password: admin123 - CHANGE THIS!)
INSERT INTO admin_users (username, password_hash, full_name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Barber Admin', 'admin@barbershop.com');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES
('shop_name', 'The Classic Barbershop'),
('shop_address', '123 Main Street'),
('shop_phone', '+1 234 567 8900'),
('shop_email', 'info@barbershop.com'),
('opening_time', '09:00'),
('closing_time', '19:00'),
('slot_duration', '30'),
('days_advance_booking', '30'),
('closed_days', '0'),
('max_daily_appointments', '20');
