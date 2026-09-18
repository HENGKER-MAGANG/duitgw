<?php use App\Core\Router; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 · DuitGW</title>
<link rel="stylesheet" href="<?= Router::url('assets/css/app.css') ?>">
</head>
<body class="dw-body dw-auth-body">
    <div class="dw-error-card">
        <span class="dw-error-stamp">DITOLAK</span>
        <h1>404</h1>
        <p>Halaman yang kamu cari tidak ada dalam catatan kami.</p>
        <a href="<?= Router::url('') ?>" class="dw-btn dw-btn-primary">Kembali</a>
    </div>
</body>
</html>
