<?php
// index.php - Main application router

session_start();

require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';

// Simple router
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Route map
$routes = [
    // Customer routes
    'home'    => ['controller' => 'CustomerController', 'file' => 'app/controllers/CustomerController.php'],
    'book'    => ['controller' => 'CustomerController', 'file' => 'app/controllers/CustomerController.php'],
    'confirm' => ['controller' => 'CustomerController', 'file' => 'app/controllers/CustomerController.php'],

    // Admin routes
    'admin'         => ['controller' => 'AdminController',    'file' => 'app/controllers/AdminController.php'],
    'admin-login'   => ['controller' => 'AdminController',    'file' => 'app/controllers/AdminController.php'],
    'admin-logout'  => ['controller' => 'AdminController',    'file' => 'app/controllers/AdminController.php'],
    'admin-book'    => ['controller' => 'AppointmentController', 'file' => 'app/controllers/AppointmentController.php'],
    'appointments'  => ['controller' => 'AppointmentController', 'file' => 'app/controllers/AppointmentController.php'],
    'services'      => ['controller' => 'ServiceController',  'file' => 'app/controllers/ServiceController.php'],
    'settings'      => ['controller' => 'SettingsController', 'file' => 'app/controllers/SettingsController.php'],

    // AJAX
    'api'           => ['controller' => 'ApiController',      'file' => 'app/controllers/ApiController.php'],
];

if (isset($routes[$page])) {
    $route = $routes[$page];
    require_once __DIR__ . '/' . $route['file'];
    $controller = new $route['controller']();
    $controller->$action();
} else {
    // 404
    require_once __DIR__ . '/app/controllers/CustomerController.php';
    $controller = new CustomerController();
    $controller->index();
}
