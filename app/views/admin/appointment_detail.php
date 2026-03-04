<?php
$pageTitle = 'Appointment Details';
require __DIR__ . '/../layouts/admin_header.php';
?>

<?php if (!empty($_GET['updated'])): ?>
<div class="alert-success">✓ Appointment updated successfully.</div>
<?php endif; ?>

<div class="detail-wrap">
    <div class="detail-actions-top">
        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn-back-sm">← Back to List</a>
        <div class="action-btns-row">
            <a href="?page=appointments&action=edit&id=<?= $appointment['id'] ?>" class="btn-edit">✏ Edit</a>
            <a href="?page=appointments&action=delete&id=<?= $appointment['id'] ?>"
               class="btn-danger"
               onclick="return confirm('Permanently delete this appointment?')">🗑 Delete</a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header status-header-<?= $appointment['status'] ?>">
            <div>
                <div class="ref-num"><?= htmlspecialchars($appointment['booking_reference']) ?></div>
                <h3><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></h3>
                <p><?= htmlspecialchars($appointment['service_name']) ?></p>
            </div>
            <div class="status-big">
                <span class="status-badge big <?= $appointment['status'] ?>"><?= ucfirst($appointment['status']) ?></span>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-section">
                <h4>Appointment</h4>
                <div class="detail-row"><span>Date</span><strong><?= date('l, d F Y', strtotime($appointment['appointment_date'])) ?></strong></div>
                <div class="detail-row"><span>Time</span><strong><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></strong></div>
                <div class="detail-row"><span>Duration</span><strong><?= $appointment['duration'] ?> minutes</strong></div>
                <div class="detail-row"><span>Service</span><strong><?= htmlspecialchars($appointment['service_name']) ?></strong></div>
                <div class="detail-row price-row"><span>Price</span><strong>€<?= number_format($appointment['price'], 2) ?></strong></div>
            </div>
            <div class="detail-section">
                <h4>Client</h4>
                <div class="detail-row"><span>Name</span><strong><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></strong></div>
                <div class="detail-row">
                    <span><?= ucfirst($appointment['communication_type']) ?></span>
                    <strong><?= htmlspecialchars($appointment['communication_value']) ?></strong>
                </div>
                <?php if ($appointment['notes']): ?>
                <div class="detail-row"><span>Notes</span><strong><?= htmlspecialchars($appointment['notes']) ?></strong></div>
                <?php endif; ?>
                <div class="detail-row"><span>Booked</span><strong><?= date('d M Y H:i', strtotime($appointment['created_at'])) ?></strong></div>
            </div>
        </div>

        <!-- Quick Status Update -->
        <div class="status-update-panel">
            <h4>Update Status</h4>
            <form action="<?= BASE_URL ?>/index.php?page=appointments&action=updateStatus" method="POST">
                <input type="hidden" name="id" value="<?= $appointment['id'] ?>">
                <input type="hidden" name="redirect" value="<?= BASE_URL ?>/index.php?page=appointments&action=view&id=<?= $appointment['id'] ?>&updated=1">
                <div class="status-btn-group">
                    <?php foreach(['confirmed','pending','completed','cancelled'] as $s): ?>
                    <button type="submit" name="status" value="<?= $s ?>"
                            class="status-change-btn <?= $s ?> <?= $appointment['status'] === $s ? 'current' : '' ?>">
                        <?= ucfirst($s) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
