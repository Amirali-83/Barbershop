<?php
// app/config/app.php
// General application configuration

define('APP_NAME', 'BarberShop');
define('APP_VERSION', '1.0.0');

// Base URL - change if your XAMPP folder name is different
define('BASE_URL', 'http://localhost/barbershop');

// Admin credentials fallback (uses DB, but this is a safety default)
define('ADMIN_SESSION_KEY', 'barber_admin_logged_in');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Timezone
date_default_timezone_set('Europe/London'); // Change to your timezone

// Working hours
define('SHOP_OPEN', '09:00');
define('SHOP_CLOSE', '19:00');
define('SLOT_MINUTES', 30); // Appointment slot duration

// Statuses
define('STATUS_CONFIRMED', 'confirmed');
define('STATUS_PENDING', 'pending');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_COMPLETED', 'completed');

// Status colors for UI
$STATUS_COLORS = [
    'confirmed'  => '#22c55e',
    'pending'    => '#f59e0b',
    'cancelled'  => '#ef4444',
    'completed'  => '#3b82f6',
];
