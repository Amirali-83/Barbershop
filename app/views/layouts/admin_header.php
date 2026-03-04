<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Barbershop Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="admin-body">

<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span>✂</span>
            <span>Barber<br>Admin</span>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/index.php?page=admin&action=dashboard"
               class="nav-item <?= (($_GET['page'] ?? '') === 'admin' && ($_GET['action'] ?? '') === 'dashboard') ? 'active' : '' ?>">
                <span class="nav-icon">◉</span> Dashboard
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=appointments"
               class="nav-item <?= (($_GET['page'] ?? '') === 'appointments') ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span> Appointments
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin-book&action=index_book"
               class="nav-item <?= (($_GET['page'] ?? '') === 'admin-book') ? 'active' : '' ?>">
                <span class="nav-icon">✚</span> New Booking
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=services"
               class="nav-item <?= (($_GET['page'] ?? '') === 'services') ? 'active' : '' ?>">
                <span class="nav-icon">✂</span> Services
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=settings"
               class="nav-item <?= (($_GET['page'] ?? '') === 'settings') ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-cog"></i></span> Settings
            </a>
            <div class="nav-divider"></div>
            <a href="<?= BASE_URL ?>/index.php" class="nav-item" target="_blank">
                <span class="nav-icon">↗</span> Booking Page
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin-logout&action=logout" class="nav-item nav-logout">
                <span class="nav-icon">⏻</span> Logout
            </a>
        </nav>
        <div class="sidebar-user">
            Logged in as<br><strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
        </div>
    </aside>

    <!-- Main content area -->
    <main class="admin-main">
        <div class="admin-topbar">
            <h2 class="page-heading"><?= htmlspecialchars($pageTitle ?? '') ?></h2>
            <div class="topbar-right">
                <?= date('l, d F Y') ?>
            </div>
        </div>
        <div class="admin-content">
