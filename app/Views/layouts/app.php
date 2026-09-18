<?php
use App\Core\Router;
use App\Core\Auth;
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? \App\Core\View::e($title) . ' · DuitGW' : 'DuitGW' ?></title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>💵</text></svg>">
<link rel="stylesheet" href="<?= Router::url('assets/css/app.css') ?>">
</head>
<body class="dw-body">

<div class="dw-shell">
    <aside class="dw-sidebar">
        <a href="<?= Router::url(Auth::isAdmin() ? 'admin/dashboard' : 'dashboard') ?>" class="dw-brand">
            <span class="dw-brand-mark">Rp</span>
            <span class="dw-brand-text">DuitGW</span>
        </a>

        <nav class="dw-nav">
            <?php if (Auth::isAdmin()): ?>
                <a href="<?= Router::url('admin/dashboard') ?>" class="dw-nav-link <?= ($active ?? '') === 'admin-dashboard' ? 'is-active' : '' ?>">
                    <span class="dw-nav-icon">◈</span> Statistik Pengguna
                </a>
            <?php else: ?>
                <a href="<?= Router::url('dashboard') ?>" class="dw-nav-link <?= ($active ?? '') === 'dashboard' ? 'is-active' : '' ?>">
                    <span class="dw-nav-icon">◈</span> Ringkasan
                </a>
                <a href="<?= Router::url('transactions') ?>" class="dw-nav-link <?= ($active ?? '') === 'transactions' ? 'is-active' : '' ?>">
                    <span class="dw-nav-icon">▤</span> Catatan Keuangan
                </a>
                <a href="<?= Router::url('transactions/create') ?>" class="dw-nav-link <?= ($active ?? '') === 'create' ? 'is-active' : '' ?>">
                    <span class="dw-nav-icon">＋</span> Catat Transaksi
                </a>
            <?php endif; ?>
        </nav>

        <div class="dw-sidebar-footer">
            <div class="dw-user-chip">
                <span class="dw-user-avatar" style="background:<?= \App\Core\View::e($user['avatar_color']) ?>"><?= strtoupper(substr($user['name'] ?? '?', 0, 1)) ?></span>
                <div class="dw-user-meta">
                    <span class="dw-user-name"><?= \App\Core\View::e($user['name']) ?></span>
                    <span class="dw-user-role"><?= $user['role'] === 'admin' ? 'Administrator' : 'Pengguna' ?></span>
                </div>
            </div>
            <form action="<?= Router::url('logout') ?>" method="POST">
                <button type="submit" class="dw-logout">Keluar</button>
            </form>
        </div>
    </aside>

    <div class="dw-main">
        <header class="dw-topbar">
            <button class="dw-menu-toggle" id="dwMenuToggle" aria-label="Buka menu">☰</button>
            <span class="dw-topbar-title"><?= isset($title) ? \App\Core\View::e($title) : '' ?></span>
        </header>

        <main class="dw-content">
            <?= $content ?>
        </main>
    </div>
</div>

<script>
document.getElementById('dwMenuToggle')?.addEventListener('click', function () {
    document.querySelector('.dw-shell').classList.toggle('sidebar-open');
});
</script>
</body>
</html>
