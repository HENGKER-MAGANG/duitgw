<?php use App\Core\Router; use App\Core\View; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar · DuitGW</title>
<link rel="stylesheet" href="<?= Router::url('assets/css/app.css') ?>">
</head>
<body class="dw-body dw-auth-body">
    <div class="dw-auth-wrap">
        <div class="dw-auth-receipt">
            <div class="dw-receipt-notch"></div>
            <div class="dw-auth-brand">
                <span class="dw-brand-mark dw-brand-mark-lg">Rp</span>
                <h1>Buat Akun</h1>
                <p>Mulai kelola arus kas pribadimu.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="dw-alert dw-alert-error"><?= View::e($error) ?></div>
            <?php endif; ?>

            <form action="<?= Router::url('register') ?>" method="POST" class="dw-form">
                <label class="dw-field">
                    <span>Nama Lengkap</span>
                    <input type="text" name="name" required value="<?= View::e($_SESSION['old']['name'] ?? '') ?>" autofocus>
                </label>
                <label class="dw-field">
                    <span>Email</span>
                    <input type="email" name="email" required value="<?= View::e($_SESSION['old']['email'] ?? '') ?>">
                </label>
                <label class="dw-field">
                    <span>Kata Sandi</span>
                    <input type="password" name="password" required minlength="6">
                </label>
                <label class="dw-field">
                    <span>Ulangi Kata Sandi</span>
                    <input type="password" name="password_confirmation" required minlength="6">
                </label>
                <button type="submit" class="dw-btn dw-btn-primary dw-btn-block">Daftar</button>
            </form>

            <p class="dw-auth-switch">Sudah punya akun? <a href="<?= Router::url('login') ?>">Masuk di sini</a></p>
        </div>
        <div class="dw-receipt-notch dw-receipt-notch-bottom"></div>
    </div>
    <?php unset($_SESSION['old']); ?>
</body>
</html>
