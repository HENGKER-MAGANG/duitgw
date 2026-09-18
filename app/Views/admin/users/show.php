<?php
use App\Core\Router;
use App\Core\View;
$title = 'Detail Pengguna';
$active = 'admin-dashboard';
?>

<a href="<?= Router::url('admin/dashboard') ?>" class="dw-link">← Kembali ke Statistik</a>

<div class="dw-panel-head" style="margin-top:1rem;">
    <div>
        <h1 class="dw-page-title" style="margin-bottom:0;"><?= View::e($targetUser['name']) ?></h1>
        <p class="dw-page-subtitle"><?= View::e($targetUser['email']) ?></p>
    </div>
</div>

<section class="dw-stat-cards">
    <div class="dw-stat-card dw-stat-card-in">
        <span class="dw-stat-label">Pemasukan</span>
        <span class="dw-stat-value"><?= View::rupiah($summary['total_masuk']) ?></span>
    </div>
    <div class="dw-stat-card dw-stat-card-out">
        <span class="dw-stat-label">Pengeluaran</span>
        <span class="dw-stat-value"><?= View::rupiah($summary['total_keluar']) ?></span>
    </div>
    <div class="dw-stat-card">
        <span class="dw-stat-label">Saldo</span>
        <span class="dw-stat-value"><?= View::rupiah($summary['saldo']) ?></span>
    </div>
    <div class="dw-stat-card">
        <span class="dw-stat-label">Jumlah Transaksi</span>
        <span class="dw-stat-value"><?= $summary['total_transaksi'] ?></span>
    </div>
</section>

<p class="dw-empty">Admin hanya dapat melihat ringkasan statistik. Rincian per-transaksi dan bukti foto bersifat privat milik pengguna.</p>
