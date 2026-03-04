<?php
$isEdit = !empty($service);
$pageTitle = $isEdit ? 'Edit Service' : 'New Service';
require __DIR__ . '/../layouts/admin_header.php';
$action = $isEdit
    ? BASE_URL . '/index.php?page=services&action=edit&id=' . $service['id']
    : BASE_URL . '/index.php?page=services&action=create';
?>

<div class="form-wrap">
<form action="<?= $action ?>" method="POST" class="admin-form">

    <div class="form-section">
        <h3><?= $isEdit ? 'Edit' : 'New' ?> Service</h3>
        <div class="input-group">
            <label>Service Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($service['name'] ?? '') ?>" placeholder="e.g. Haircut" required>
        </div>
        <div class="input-group">
            <label>Description</label>
            <textarea name="description" rows="2" placeholder="Brief description of the service..."><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
        </div>
        <div class="input-row-2">
            <div class="input-group">
                <label>Duration (minutes) *</label>
                <input type="number" name="duration" value="<?= $service['duration'] ?? 30 ?>" min="5" step="5" required>
            </div>
            <div class="input-group">
                <label>Price (€) *</label>
                <input type="number" name="price" value="<?= $service['price'] ?? '' ?>" min="0" step="0.50" placeholder="25.00" required>
            </div>
        </div>
        <?php if ($isEdit): ?>
        <div class="input-group">
            <label class="checkbox-label">
                <input type="checkbox" name="active" <?= ($service['active'] ?? 1) ? 'checked' : '' ?>>
                Service is active (visible to customers)
            </label>
        </div>
        <?php endif; ?>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-primary"><?= $isEdit ? 'Save Changes' : '✚ Create Service' ?></button>
        <a href="<?= BASE_URL ?>/index.php?page=services" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
