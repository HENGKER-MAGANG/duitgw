<?php
use App\Core\Router;
use App\Core\View;
$title = 'Statistik Pengguna';
$active = 'admin-dashboard';
?>

<h1 class="dw-page-title">Statistik Seluruh Pengguna</h1>
<p class="dw-page-subtitle">Tampilan ini hanya untuk memantau. Admin tidak dapat mengubah data keuangan pengguna.</p>

<section class="dw-stat-cards">
    <div class="dw-stat-card">
        <span class="dw-stat-label">Total Pengguna</span>
        <span class="dw-stat-value"><?= $totalUsers ?></span>
    </div>
    <div class="dw-stat-card dw-stat-card-in">
        <span class="dw-stat-label">Total Pemasukan</span>
        <span class="dw-stat-value"><?= View::rupiah($totalMasuk) ?></span>
    </div>
    <div class="dw-stat-card dw-stat-card-out">
        <span class="dw-stat-label">Total Pengeluaran</span>
        <span class="dw-stat-value"><?= View::rupiah($totalKeluar) ?></span>
    </div>
    <div class="dw-stat-card">
        <span class="dw-stat-label">Total Transaksi</span>
        <span class="dw-stat-value"><?= $totalTransaksi ?></span>
    </div>
</section>

<div class="dw-panel">
    <h2 class="dw-panel-title">Tren Platform (6 Bulan Terakhir)</h2>
    <div id="react-admin-chart" data-api="<?= Router::url('api/stats/admin') ?>"></div>
</div>

<div class="dw-panel">
    <h2 class="dw-panel-title">Rincian per Pengguna</h2>
    <div id="react-admin-stats" data-api="<?= Router::url('api/stats/admin') ?>" data-user-url="<?= Router::url('admin/users/') ?>"></div>
</div>

<script type="module" src="<?= Router::url('assets/js/app.js') ?>"></script>
