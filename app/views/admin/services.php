<?php
$pageTitle = 'Services';
require __DIR__ . '/../layouts/admin_header.php';
?>

<?php if (!empty($message)): ?>
<div class="alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="page-top-actions">
    <a href="?page=services&action=create" class="btn-primary">✚ Add Service</a>
</div>

<div class="appt-table-wrap">
<table class="appt-table">
    <thead>
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($services as $s): ?>
    <tr>
        <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
        <td class="td-desc"><?= htmlspecialchars($s['description']) ?></td>
        <td><?= $s['duration'] ?> min</td>
        <td>€<?= number_format($s['price'], 2) ?></td>
        <td><span class="status-badge <?= $s['active'] ? 'confirmed' : 'cancelled' ?>"><?= $s['active'] ? 'Active' : 'Inactive' ?></span></td>
        <td class="td-actions">
            <a href="?page=services&action=edit&id=<?= $s['id'] ?>" class="action-btn edit-btn">✏ Edit</a>
            <a href="?page=services&action=delete&id=<?= $s['id'] ?>"
               class="action-btn del-btn"
               onclick="return confirm('Delete this service?')">🗑</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php require __DIR__ . '/../layouts/admin_footer.php'; ?>
