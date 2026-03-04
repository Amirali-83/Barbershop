<?php
$pageTitle = 'New Booking';
require __DIR__ . '/../layouts/admin_header.php';
$fd = $form_data ?? [];
?>

<?php if (!empty($_GET['booked'])): ?>
<div class="alert-success">✓ Booking created! Reference: <strong><?= htmlspecialchars($_GET['ref'] ?? '') ?></strong></div>
<?php endif; ?>

<?php if (!empty($form_errors)): ?>
<div class="alert-error">
    <?php foreach ($form_errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="form-wrap">
<form action="<?= BASE_URL ?>/index.php?page=admin-book&action=submit" method="POST" class="admin-form">

    <div class="form-section">
        <h3>Client Details</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>First Name *</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($fd['first_name'] ?? '') ?>" placeholder="John" required>
            </div>
            <div class="input-group">
                <label>Last Name *</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($fd['last_name'] ?? '') ?>" placeholder="Smith" required>
            </div>
        </div>
        <div class="input-row-2">
            <div class="input-group">
                <label>Communication Method</label>
                <select name="communication_type" id="a_comm_type" onchange="toggleCommValue(this.value)">
                    <option value="telephone" <?= ($fd['communication_type'] ?? '') === 'telephone' ? 'selected' : '' ?>>Telephone</option>
                    <option value="email" <?= ($fd['communication_type'] ?? '') === 'email' ? 'selected' : '' ?>>Email</option>
                </select>
            </div>
            <div class="input-group">
                <label id="comm_val_label">Phone Number</label>
                <input type="text" name="communication_value" id="a_comm_value"
                       value="<?= htmlspecialchars($fd['communication_value'] ?? '') ?>"
                       placeholder="+44 7700 900000">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Service</h3>
        <div class="input-group">
            <label>Select Service *</label>
            <select name="service_id" id="a_service" required onchange="loadAdminSlots()">
                <option value="">— Choose a service —</option>
                <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>"
                        data-duration="<?= $s['duration'] ?>"
                        data-price="<?= $s['price'] ?>"
                        <?= ($fd['service_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?> (<?= $s['duration'] ?>min — €<?= number_format($s['price'], 2) ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-section">
        <h3>Date & Time</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>Date *</label>
                <input type="date" name="appointment_date" id="a_date"
                       value="<?= htmlspecialchars($fd['appointment_date'] ?? '') ?>"
                       min="<?= date('Y-m-d') ?>"
                       onchange="loadAdminSlots()" required>
            </div>
            <div class="input-group">
                <label>Time *</label>
                <select name="appointment_time" id="a_time_select" required>
                    <option value="">— Pick date & service first —</option>
                    <?php if (!empty($fd['appointment_time'])): ?>
                    <option value="<?= htmlspecialchars($fd['appointment_time']) ?>" selected>
                        <?= date('g:i A', strtotime($fd['appointment_time'])) ?>
                    </option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Status & Notes</h3>
        <div class="input-row-2">
            <div class="input-group">
                <label>Status</label>
                <select name="status">
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            <div class="input-group">
                <label>Notes (optional)</label>
                <input type="text" name="notes" value="<?= htmlspecialchars($fd['notes'] ?? '') ?>" placeholder="e.g. Fade on sides">
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary">✚ Create Booking</button>
        <a href="<?= BASE_URL ?>/index.php?page=appointments" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

function toggleCommValue(type) {
    const label = document.getElementById('comm_val_label');
    const input = document.getElementById('a_comm_value');
    if (type === 'email') {
        label.textContent = 'Email Address';
        input.type = 'email';
        input.placeholder = 'john@example.com';
    } else {
        label.textContent = 'Phone Number';
        input.type = 'tel';
        input.placeholder = '+44 7700 900000';
    }
}

function loadAdminSlots() {
    const date = document.getElementById('a_date').value;
    const serviceId = document.getElementById('a_service').value;
    const select = document.getElementById('a_time_select');
    const currentTime = '<?= htmlspecialchars($fd['appointment_time'] ?? '') ?>';

    if (!date || !serviceId) {
        select.innerHTML = '<option value="">— Pick date & service first —</option>';
        return;
    }

    select.innerHTML = '<option>Loading...</option>';

    fetch(`${BASE_URL}/index.php?page=api&action=slots&date=${date}&service_id=${serviceId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.slots || data.slots.length === 0) {
                select.innerHTML = '<option value="">No slots available</option>';
                return;
            }
            select.innerHTML = '<option value="">— Choose a time —</option>';
            data.slots.forEach(slot => {
                const opt = document.createElement('option');
                opt.value = slot.time;
                opt.textContent = slot.label + (slot.available ? '' : ' (taken)');
                opt.disabled = !slot.available;
                if (slot.time === currentTime) opt.selected = true;
                select.appendChild(opt);
            });
        });
}

// Init
toggleCommValue(document.getElementById('a_comm_type').value);
if (document.getElementById('a_date').value && document.getElementById('a_service').value) {
    loadAdminSlots();
}
</script>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
