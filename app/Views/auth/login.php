<?php use App\Core\Router; use App\Core\View; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk · DuitGW</title>
<link rel="stylesheet" href="<?= Router::url('assets/css/app.css') ?>">
</head>
<body class="dw-body dw-auth-body">
    <div class="dw-auth-wrap">
        <div class="dw-auth-receipt">
            <div class="dw-receipt-notch"></div>
            <div class="dw-auth-brand">
                <span class="dw-brand-mark dw-brand-mark-lg">Rp</span>
                <h1>DuitGW</h1>
                <p>Catat setiap rupiah, lampirkan buktinya.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="dw-alert dw-alert-error"><?= View::e($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="dw-alert dw-alert-success"><?= View::e($success) ?></div>
            <?php endif; ?>

            <form action="<?= Router::url('login') ?>" method="POST" class="dw-form">
                <label class="dw-field">
                    <span>Email</span>
                    <input type="email" name="email" required placeholder="nama@email.com" autofocus>
                </label>
                <label class="dw-field">
                    <span>Kata Sandi</span>
                    <input type="password" name="password" required placeholder="••••••••">
                </label>
                <button type="submit" class="dw-btn dw-btn-primary dw-btn-block">Masuk</button>
            </form>

            <p class="dw-auth-switch">Belum punya akun? <a href="<?= Router::url('register') ?>">Daftar di sini</a></p>

            <div class="dw-receipt-divider"></div>
            <p class="dw-auth-hint">Demo admin: admin@duitgw.test / admin123</p>
        </div>
        <div class="dw-receipt-notch dw-receipt-notch-bottom"></div>
    </div>
</body>
</html>
