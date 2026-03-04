<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed — <?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/customer.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <div class="logo">
            <span class="logo-icon">✂</span>
            <span class="logo-text"><?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?></span>
        </div>
    </div>
</header>

<main class="confirm-main">
    <?php if ($appointment): ?>
    <div class="confirm-card">
        <div class="confirm-icon">✓</div>
        <h1>You're All Set!</h1>
        <p class="confirm-sub">Your appointment has been confirmed.</p>

        <div class="confirm-ref">
            Booking Reference: <strong><?= htmlspecialchars($appointment['booking_reference']) ?></strong>
        </div>

        <div class="confirm-details">
            <div class="detail-row">
                <span class="detail-label">Name</span>
                <span class="detail-value"><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service</span>
                <span class="detail-value"><?= htmlspecialchars($appointment['service_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date</span>
                <span class="detail-value"><?= date('l, d F Y', strtotime($appointment['appointment_date'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Time</span>
                <span class="detail-value"><?= date('g:i A', strtotime($appointment['appointment_time'])) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value"><?= $appointment['duration'] ?> minutes</span>
            </div>
            <div class="detail-row price-highlight">
                <span class="detail-label">Price</span>
                <span class="detail-value">€<?= number_format($appointment['price'], 2) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><?= ucfirst($appointment['communication_type']) ?></span>
                <span class="detail-value"><?= htmlspecialchars($appointment['communication_value']) ?></span>
            </div>
            <?php if ($appointment['notes']): ?>
            <div class="detail-row">
                <span class="detail-label">Notes</span>
                <span class="detail-value"><?= htmlspecialchars($appointment['notes']) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="confirm-address">
            <strong><?= htmlspecialchars($settings['shop_name'] ?? '') ?></strong><br>
            <?= htmlspecialchars($settings['shop_address'] ?? '') ?><br>
            <?= htmlspecialchars($settings['shop_phone'] ?? '') ?>
        </div>

        <a href="<?= BASE_URL ?>/index.php" class="btn-back">← Book Another Appointment</a>
    </div>

    <?php else: ?>
    <div class="confirm-card">
        <div class="confirm-icon error">✕</div>
        <h1>Booking Not Found</h1>
        <p>We couldn't find that booking reference.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn-back">← Back to Booking</a>
    </div>
    <?php endif; ?>
</main>

<footer class="site-footer">
    <p><?= htmlspecialchars($settings['shop_name'] ?? 'The Barbershop') ?> — <?= htmlspecialchars($settings['shop_address'] ?? '') ?></p>
    <p><a href="<?= BASE_URL ?>/index.php?page=admin-login">Admin Login</a></p>
</footer>

</body>
</html>
