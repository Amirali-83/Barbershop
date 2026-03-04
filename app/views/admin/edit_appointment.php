<?php
$pageTitle = 'Edit Appointment';
require __DIR__ . '/../layouts/admin_header.php';
$fd = $form_data ?? $appointment ?? [];
?>

<?php if (!empty($form_errors)): ?>
<div class="alert-error">
    <?php foreach ($form_errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="form-wrap">
<form action="<?= BASE_URL ?>/index.php?page=appointments&action=update" method="POST" class="admin-form">
    <input type="hidden" name="id" value="<?= $appointment['id'] ?>">

    <div class="form-section">
        <h3>Client Details</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>First Name *</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($fd['first_name'] ?? '') ?>" required>
            </div>
            <div class="input-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($fd['last_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="input-row-2">
            <div class="input-group">
                <label>Communication</label>
                <select name="communication_type" onchange="toggleCommLabel(this.value)">
                    <option value="telephone" <?= ($fd['communication_type'] ?? '') === 'telephone' ? 'selected' : '' ?>>Telephone</option>
                    <option value="email" <?= ($fd['communication_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                </select>
            </div>
            <div class="input-group">
                <label>Contact Value</label>
                <input type="text" name="communication_value" value="<?= htmlspecialchars($fd['communication_value'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Service</h3>
        <div class="input-group">
            <label>Service *</label>
            <select name="service_id" id="e_service" required onchange="loadEditSlots()">
                <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($fd['service_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?> (<?= $s['duration'] ?>min)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h3>Date &amp; Time</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>Date *</label>
                <input type="date" name="appointment_date" id="e_date"
                       value="<?= htmlspecialchars($fd['appointment_date'] ?? '') ?>"
                       onchange="loadEditSlots()" required>
            </div>
            <div class="input-group">
                <label>Time *</label>
                <select name="appointment_time" id="e_time_select" required>
                    <option value="<?= htmlspecialchars($fd['appointment_time'] ?? '') ?>">
                        <?= !empty($fd['appointment_time']) ? date('g:i A', strtotime($fd['appointment_time'])) : '— Select —' ?>
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Status &amp; Notes</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>Status</label>
                <select name="status">
                    <?php foreach(['confirmed','pending','completed','cancelled'] as $s): ?>
                    <option value="<?= $s ?>" <?= ($fd['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group">
                <label>Notes</label>
                <input type="text" name="notes" value="<?= htmlspecialchars($fd['notes'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">Save Changes</button>
        <a href="?page=appointments&action=view&id=<?= $appointment['id'] ?>" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const currentTime = '<?= htmlspecialchars($fd['appointment_time'] ?? '') ?>';
const appointmentId = <?= $appointment['id'] ?>;

function loadEditSlots() {
    const date = document.getElementById('e_date').value;
    const serviceId = document.getElementById('e_service').value;
    const select = document.getElementById('e_time_select');
    if (!date || !serviceId) return;
    select.innerHTML = '<option>Loading...</option>';
    fetch(`${BASE_URL}/index.php?page=api&action=slots&date=${date}&service_id=${serviceId}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '';
            if (!data.slots || data.slots.length === 0) {
                select.innerHTML = '<option value="">No slots available</option>';
                return;
            }
            data.slots.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.time;
                opt.textContent = slot.label + (!slot.available && slot.time !== currentTime ? ' (taken)' : '');
                opt.disabled = !slot.available && slot.time !== currentTime;
                if (slot.time === currentTime) opt.selected = true;
                select.appendChild(opt);
            });
            // If none selected, select first available
            if (!select.value) {
                const first = select.querySelector('option:not([disabled])');
                if (first) first.selected = true;
            }
        });
}

window.addEventListener('DOMContentLoaded', loadEditSlots);
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
