<?php
$pageTitle = 'Dashboard';
require __DIR__ . '/../layouts/admin_header.php';
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-body">
            <div class="stat-num"><?= $stats['today'] ?></div>
            <div class="stat-label">Today's Bookings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-clock"></i></div>
        <div class="stat-body">
            <div class="stat-num"><?= $stats['upcoming_today'] ?></div>
            <div class="stat-label">Upcoming Today</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
        <div class="stat-body">
            <div class="stat-num"><?= $stats['week'] ?></div>
            <div class="stat-label">This Week</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
        <div class="stat-body">
            <div class="stat-num"><?= $stats['month'] ?></div>
            <div class="stat-label">This Month</div>
        </div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon">€</div>
        <div class="stat-body">
            <div class="stat-num">€<?= number_format($stats['revenue'], 2) ?></div>
            <div class="stat-label">Revenue (Completed)</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="<?= BASE_URL ?>/index.php?page=admin-book&action=index_book" class="qa-btn primary"><i class="fas fa-plus"></i> New Booking</a>
    <a href="<?= BASE_URL ?>/index.php?page=appointments&filter=today" class="qa-btn"><i class="fas fa-list"></i> Today's List</a>
    <a href="<?= BASE_URL ?>/index.php?page=appointments&view=calendar" class="qa-btn"><i class="fas fa-calendar-day"></i> Calendar</a>
    <a href="<?= BASE_URL ?>/index.php?page=appointments&filter=week" class="qa-btn"><i class="fas fa-calendar-week"></i> This Week</a>
</div>

<div class="dashboard-cols">
    <!-- Today's Appointments -->
    <div class="dash-panel">
        <div class="panel-header">
            <h3>Today's Appointments</h3>
            <a href="<?= BASE_URL ?>/index.php?page=appointments&filter=today">View All →</a>
        </div>
        <?php if (empty($todayAppointments)): ?>
            <div class="empty-state">No appointments today.</div>
        <?php else: ?>
        <div class="appt-mini-list">
            <?php foreach ($todayAppointments as $appt): ?>
            <div class="appt-mini-item status-<?= $appt['status'] ?>">
                <div class="appt-mini-time"><?= date('g:i A', strtotime($appt['appointment_time'])) ?></div>
                <div class="appt-mini-info">
                    <strong><?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></strong>
                    <span><?= htmlspecialchars($appt['service_name']) ?></span>
                </div>
                <div class="appt-mini-right">
                    <span class="status-badge <?= $appt['status'] ?>"><?= ucfirst($appt['status']) ?></span>
                    <a href="<?= BASE_URL ?>/index.php?page=appointments&action=view&id=<?= $appt['id'] ?>" class="mini-view">→</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- This Week -->
    <div class="dash-panel">
        <div class="panel-header">
            <h3>This Week</h3>
            <a href="<?= BASE_URL ?>/index.php?page=appointments&filter=week">View All →</a>
        </div>
        <?php if (empty($weekAppointments)): ?>
            <div class="empty-state">No appointments this week.</div>
        <?php else: ?>
        <div class="appt-mini-list">
            <?php foreach (array_slice($weekAppointments, 0, 8) as $appt): ?>
            <div class="appt-mini-item status-<?= $appt['status'] ?>">
                <div class="appt-mini-time">
                    <span><?= date('D', strtotime($appt['appointment_date'])) ?></span>
                    <span class="time-sm"><?= date('g:i A', strtotime($appt['appointment_time'])) ?></span>
                </div>
                <div class="appt-mini-info">
                    <strong><?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></strong>
                    <span><?= htmlspecialchars($appt['service_name']) ?></span>
                </div>
                <div class="appt-mini-right">
                    <span class="status-badge <?= $appt['status'] ?>"><?= ucfirst($appt['status']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
