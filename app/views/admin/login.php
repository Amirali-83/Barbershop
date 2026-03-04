<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Barbershop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="login-body">

<div class="login-wrap">
    <div class="login-card">
        <div class="login-logo">
            <span class="logo-icon-lg">✂</span>
            <h1>Barbershop</h1>
            <p>Admin Portal</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['timeout'])): ?>
        <div class="alert-warn">Your session expired. Please log in again.</div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/index.php?page=admin-login&action=login" method="POST" class="login-form">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       placeholder="admin" required autofocus>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Sign In →</button>
        </form>

        <div class="login-hint">
            Default: admin / admin123 — <strong>change after first login</strong>
        </div>

        <a href="<?= BASE_URL ?>/index.php" class="back-link">← Back to Booking Page</a>
    </div>
</div>

</body>
</html>
