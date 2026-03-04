<?php
$pageTitle = 'Settings';
require __DIR__ . '/../layouts/admin_header.php';

$closedDaysArr = explode(',', $settings['closed_days'] ?? '0');
$closedDaysArr = array_map('trim', $closedDaysArr);

$daysOfWeek = [
    '0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday',
    '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday'
];
?>

<?php if (!empty($message)): ?>
<div class="alert-success">✓ <?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="settings-wrap">

    <!-- Shop Settings -->
    <form action="<?= BASE_URL ?>/index.php?page=settings&action=save" method="POST" class="admin-form">

        <div class="form-section">
            <h3>Shop Information</h3>
            <div class="input-row-2">
                <div class="input-group">
                    <label>Shop Name</label>
                    <input type="text" name="shop_name" value="<?= htmlspecialchars($settings['shop_name'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Phone</label>
                    <input type="text" name="shop_phone" value="<?= htmlspecialchars($settings['shop_phone'] ?? '') ?>">
                </div>
            </div>
            <div class="input-row-2">
                <div class="input-group">
                    <label>Address</label>
                    <input type="text" name="shop_address" value="<?= htmlspecialchars($settings['shop_address'] ?? '') ?>">
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="shop_email" value="<?= htmlspecialchars($settings['shop_email'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Opening Hours</h3>
            <div class="input-row-2">
                <div class="input-group">
                    <label>Opening Time</label>
                    <input type="time" name="opening_time" value="<?= htmlspecialchars($settings['opening_time'] ?? '09:00') ?>">
                </div>
                <div class="input-group">
                    <label>Closing Time</label>
                    <input type="time" name="closing_time" value="<?= htmlspecialchars($settings['closing_time'] ?? '19:00') ?>">
                </div>
            </div>
            <div class="input-row-2">
                <div class="input-group">
                    <label>Slot Duration (minutes)</label>
                    <select name="slot_duration">
                        <?php foreach([15,20,30,45,60] as $d): ?>
                        <option value="<?= $d ?>" <?= ($settings['slot_duration'] ?? 30) == $d ? 'selected' : '' ?>><?= $d ?> minutes</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label>Days Advance Booking</label>
                    <input type="number" name="days_advance_booking" value="<?= htmlspecialchars($settings['days_advance_booking'] ?? 30) ?>" min="1" max="365">
                </div>
            </div>

            <div class="input-group">
                <label>Closed Days</label>
                <div class="days-checkboxes">
                    <?php foreach ($daysOfWeek as $val => $name): ?>
                    <label class="day-check <?= in_array($val, $closedDaysArr) ? 'checked' : '' ?>">
                        <input type="checkbox" name="closed_days_arr[]" value="<?= $val ?>"
                               <?= in_array($val, $closedDaysArr) ? 'checked' : '' ?>
                               onchange="this.closest('label').classList.toggle('checked', this.checked)">
                        <?= $name ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>

    <!-- Change Password -->
    <div class="form-section pw-section">
        <h3>Change Admin Password</h3>
        <form action="<?= BASE_URL ?>/index.php?page=settings&action=changePassword" method="POST" class="admin-form">
            <div class="input-row-3">
                <div class="input-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="••••••••">
                </div>
                <div class="input-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="••••••••" minlength="6">
                </div>
                <div class="input-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="btn-secondary">Change Password</button>
        </form>
    </div>

</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
