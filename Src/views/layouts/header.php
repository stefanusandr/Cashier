<?php
use SmartKasir\Utils\SessionManager;
$user = SessionManager::getUser();
$isLoggedIn = SessionManager::isLoggedIn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'SmartKasir POS') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php if ($isLoggedIn): ?>
<header class="navbar">
    <div style="display: flex; align-items: center;">
        <a href="/dashboard" class="nav-brand">
            <div class="nav-logo-badge">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div class="nav-brand-text">Smart<span>Kasir</span></div>
        </a>
        <div class="nav-store-badge">
            <i class="fa-solid fa-store" style="color: var(--primary);"></i>
            <span><?= htmlspecialchars($user['store_name']) ?></span>
        </div>
    </div>

    <div class="nav-user-menu">
        <div class="user-profile-pill">
            <div class="user-avatar">
                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
            </div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($user['full_name']) ?></div>
                <span class="badge-role <?= $user['role'] === 'ADMIN' ? 'badge-admin' : 'badge-cashier' ?>">
                    <?= htmlspecialchars($user['role']) ?>
                </span>
            </div>
        </div>

        <a href="/logout" class="btn-logout" title="Keluar dari sistem" id="btn-logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </div>
</header>
<?php endif; ?>
