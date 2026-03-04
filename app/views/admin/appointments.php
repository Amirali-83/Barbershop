<?php
$pageTitle = 'Appointments';
require __DIR__ . '/../layouts/admin_header.php';

$calYear  = (int)($_GET['year'] ?? date('Y'));
$calMonth = (int)($_GET['month'] ?? date('m'));
$prevMonth = $calMonth - 1; $prevYear = $calYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
$nextMonth = $calMonth + 1; $nextYear = $calYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
?>

<!-- Toolbar -->
<div class="appt-toolbar">
    <div class="view-tabs">
        <a href="?page=appointments&view=list&filter=<?= $_GET['filter'] ?? 'today' ?>"
           class="tab-btn <?= ($view ?? 'list') !== 'calendar' ? 'active' : '' ?>"><i class="fas fa-list"></i> List</a>
        <a href="?page=appointments&view=calendar&month=<?= $calMonth ?>&year=<?= $calYear ?>"
           class="tab-btn <?= ($view ?? 'list') === 'calendar' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Calendar</a>
    </div>

    <?php if (($view ?? 'list') !== 'calendar'): ?>
    <div class="filter-tabs">
        <a href="?page=appointments&view=list&filter=today" class="filter-btn <?= ($_GET['filter'] ?? 'today') === 'today' ? 'active' : '' ?>">Today</a>
        <a href="?page=appointments&view=list&filter=week" class="filter-btn <?= ($_GET['filter'] ?? '') === 'week' ? 'active' : '' ?>">This Week</a>
        <a href="?page=appointments&view=list&filter=month&month=<?= date('m') ?>&year=<?= date('Y') ?>" class="filter-btn <?= ($_GET['filter'] ?? '') === 'month' ? 'active' : '' ?>">This Month</a>
        <a href="?page=appointments&view=list&filter=all" class="filter-btn <?= ($_GET['filter'] ?? '') === 'all' ? 'active' : '' ?>">All</a>
    </div>
    <div class="filter-date">
        <input type="date" id="jumpDate" value="<?= $_GET['date'] ?? date('Y-m-d') ?>">
        <button onclick="jumpToDate()" class="btn-sm">Go</button>
    </div>
    <?php endif; ?>

    <div class="status-filter">
        <select id="statusFilter" onchange="filterByStatus(this.value)">
            <option value="">All Statuses</option>
            <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </div>

    <a href="<?= BASE_URL ?>/index.php?page=admin-book&action=index_book" class="btn-primary-sm">✚ New Booking</a>
</div>

<!-- CALENDAR VIEW -->
<?php if (($view ?? 'list') === 'calendar'): ?>

<div class="calendar-wrap">
    <div class="cal-nav">
        <a href="?page=appointments&view=calendar&month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="cal-nav-btn">← Prev</a>
        <h3 class="cal-month-title"><?= date('F Y', mktime(0,0,0,$calMonth,1,$calYear)) ?></h3>
        <a href="?page=appointments&view=calendar&month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="cal-nav-btn">Next →</a>
    </div>

    <div class="calendar-grid">
        <?php foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day): ?>
        <div class="cal-header-cell"><?= $day ?></div>
        <?php endforeach; ?>

        <?php
        $firstDay = mktime(0,0,0,$calMonth,1,$calYear);
        $daysInMonth = date('t', $firstDay);
        $startDow = (int)date('N', $firstDay); // 1=Mon

        // Empty cells before
        for ($i = 1; $i < $startDow; $i++): ?>
        <div class="cal-cell empty"></div>
        <?php endfor;

        for ($d = 1; $d <= $daysInMonth; $d++):
            $dateStr = sprintf('%04d-%02d-%02d', $calYear, $calMonth, $d);
            $dayData = $calendarData[$dateStr] ?? null;
            $isToday = ($dateStr === date('Y-m-d'));
            $total = $dayData ? ($dayData['count'] - $dayData['cancelled']) : 0;
        ?>
        <div class="cal-cell <?= $isToday ? 'today' : '' ?> <?= $total > 0 ? 'has-appts' : '' ?>"
             onclick="loadDayAppointments('<?= $dateStr ?>')">
            <div class="cal-day-num"><?= $d ?></div>
            <?php if ($total > 0): ?>
            <div class="cal-appt-count"><?= $total ?> appt<?= $total > 1 ? 's' : '' ?></div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
</div>

<!-- Day appointments panel -->
<div id="day-panel" class="day-panel" style="display:none;">
    <div class="day-panel-header">
        <h4 id="day-panel-title"></h4>
        <button onclick="document.getElementById('day-panel').style.display='none'">✕</button>
    </div>
    <div id="day-panel-content" class="appt-list"></div>
</div>

<!-- Pre-load selected day if coming from calendar click -->
<?php if (!empty($appointments) && !empty($_GET['date'])): ?>
<script>
window.addEventListener('DOMContentLoaded', function() {
    document.getElementById('day-panel').style.display = 'block';
    document.getElementById('day-panel-title').textContent = new Date('<?= $_GET['date'] ?>T12:00:00').toLocaleDateString('en-GB', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
    renderAppointments(<?= json_encode($appointments) ?>);
});
</script>
<?php endif; ?>

<?php else: ?>

<!-- LIST VIEW -->
<div class="list-title-bar">
    <h3><?= htmlspecialchars($title ?? '') ?></h3>
    <span class="count-badge"><?= count($appointments) ?> appointment<?= count($appointments) !== 1 ? 's' : '' ?></span>
</div>

<?php if (empty($appointments)): ?>
<div class="empty-state-lg">
    <div><i class="fas fa-calendar-alt"></i></div>
    <p>No appointments found for this period.</p>
    <a href="<?= BASE_URL ?>/index.php?page=admin-book&action=index_book" class="btn-primary-sm">✚ Add Booking</a>
</div>
<?php else: ?>

<div class="appt-table-wrap">
<table class="appt-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Client</th>
            <th>Service</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Price</th>
            <th>Ref</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($appointments as $appt): ?>
    <tr class="appt-row status-row-<?= $appt['status'] ?>">
        <td class="td-time">
            <div><?= date('D d M', strtotime($appt['appointment_date'])) ?></div>
            <strong><?= date('g:i A', strtotime($appt['appointment_time'])) ?></strong>
        </td>
        <td class="td-client">
            <strong><?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></strong>
        </td>
        <td class="td-service">
            <?= htmlspecialchars($appt['service_name']) ?>
            <span class="dur-tag"><?= $appt['duration'] ?>m</span>
        </td>
        <td class="td-contact">
            <span class="comm-label"><?= $appt['communication_type'] === 'telephone' ? '📱' : '✉' ?></span>
            <?= htmlspecialchars($appt['communication_value']) ?>
        </td>
        <td>
            <select class="status-select status-<?= $appt['status'] ?>"
                    onchange="updateStatus(<?= $appt['id'] ?>, this.value, this)">
                <option value="confirmed"  <?= $appt['status'] === 'confirmed'  ? 'selected' : '' ?>>Confirmed</option>
                <option value="pending"    <?= $appt['status'] === 'pending'    ? 'selected' : '' ?>>Pending</option>
                <option value="completed"  <?= $appt['status'] === 'completed'  ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled"  <?= $appt['status'] === 'cancelled'  ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </td>
        <td class="td-price">€<?= number_format($appt['price'], 2) ?></td>
        <td class="td-ref"><?= htmlspecialchars($appt['booking_reference']) ?></td>
        <td class="td-actions">
            <a href="?page=appointments&action=view&id=<?= $appt['id'] ?>" class="action-btn view-btn" title="View">👁</a>
            <a href="?page=appointments&action=edit&id=<?= $appt['id'] ?>" class="action-btn edit-btn" title="Edit">✏</a>
            <a href="?page=appointments&action=delete&id=<?= $appt['id'] ?>"
               class="action-btn del-btn"
               title="Delete"
               onclick="return confirm('Delete this appointment?')">🗑</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>
<?php endif; ?>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const currentView = '<?= $view ?? 'list' ?>';
const currentFilter = '<?= $_GET['filter'] ?? 'today' ?>';
const currentDate = '<?= $_GET['date'] ?? date('Y-m-d') ?>';
const currentMonth = <?= $calMonth ?>;
const currentYear = <?= $calYear ?>;

function jumpToDate() {
    const d = document.getElementById('jumpDate').value;
    if (d) window.location = `${BASE_URL}/index.php?page=appointments&view=list&filter=date&date=${d}`;
}

function filterByStatus(status) {
    const url = new URL(window.location.href);
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    window.location = url.toString();
}

function updateStatus(id, status, selectEl) {
    selectEl.className = `status-select status-${status}`;
    fetch(`${BASE_URL}/index.php?page=appointments&action=updateStatus`, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${id}&status=${status}&ajax=1`
    }).then(r => r.json()).then(data => {
        if (!data.success) alert('Failed to update status');
        else {
            const row = selectEl.closest('tr');
            if (row) {
                row.className = `appt-row status-row-${status}`;
            }
        }
    });
}

function loadDayAppointments(date) {
    const panel = document.getElementById('day-panel');
    const title = document.getElementById('day-panel-title');
    const content = document.getElementById('day-panel-content');

    title.textContent = new Date(date + 'T12:00:00').toLocaleDateString('en-GB', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
    content.innerHTML = '<div class="loading-sm">Loading...</div>';
    panel.style.display = 'block';

    fetch(`${BASE_URL}/index.php?page=appointments&view=calendar&date=${date}&year=${currentYear}&month=${currentMonth}&ajax=1`)
        .then(() => {
            window.location = `${BASE_URL}/index.php?page=appointments&view=calendar&date=${date}&year=${currentYear}&month=${currentMonth}`;
        });
}

function renderAppointments(appts) {
    const content = document.getElementById('day-panel-content');
    if (!appts || appts.length === 0) {
        content.innerHTML = '<div class="empty-state">No appointments this day.</div>';
        return;
    }
    content.innerHTML = appts.map(a => `
        <div class="appt-mini-item status-${a.status}">
            <div class="appt-mini-time">${formatTime(a.appointment_time)}</div>
            <div class="appt-mini-info">
                <strong>${a.first_name} ${a.last_name}</strong>
                <span>${a.service_name}</span>
            </div>
            <div class="appt-mini-right">
                <span class="status-badge ${a.status}">${cap(a.status)}</span>
                <a href="${BASE_URL}/index.php?page=appointments&action=view&id=${a.id}" class="mini-view">→</a>
            </div>
        </div>
    `).join('');
}

function formatTime(t) {
    const [h, m] = t.split(':');
    const hr = parseInt(h);
    return `${hr > 12 ? hr-12 : hr}:${m} ${hr >= 12 ? 'PM' : 'AM'}`;
}
function cap(s) { return s.charAt(0).toUpperCase() + s.slice(1); }
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
