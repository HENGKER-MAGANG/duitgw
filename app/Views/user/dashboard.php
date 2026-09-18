<?php
use App\Core\Router;
use App\Core\View;
$title = 'Ringkasan';
$active = 'dashboard';
?>

<?php if (!empty($success)): ?>
    <div class="dw-alert dw-alert-success"><?= View::e($success) ?></div>
<?php endif; ?>

<section class="dw-note-hero">
    <div class="dw-note-hero-row">
        <div>
            <span class="dw-note-label">Saldo Saat Ini</span>
            <div class="dw-note-amount"><?= View::rupiah($summary['saldo']) ?></div>
        </div>
        <span class="dw-note-serial">No. <?= str_pad((string) $summary['total_transaksi'], 6, '0', STR_PAD_LEFT) ?></span>
    </div>
    <div class="dw-note-hero-split">
        <div class="dw-note-hero-item">
            <span class="dw-dot dw-dot-in"></span> Pemasukan
            <strong><?= View::rupiah($summary['total_masuk']) ?></strong>
        </div>
        <div class="dw-note-hero-item">
            <span class="dw-dot dw-dot-out"></span> Pengeluaran
            <strong><?= View::rupiah($summary['total_keluar']) ?></strong>
        </div>
    </div>
    <a href="<?= Router::url('transactions/create') ?>" class="dw-btn dw-btn-light">＋ Catat Transaksi Baru</a>
</section>

<section class="dw-grid-2">
    <div class="dw-panel">
        <h2 class="dw-panel-title">Tren 6 Bulan Terakhir</h2>
        <div id="react-user-chart" data-api="<?= Router::url('api/stats/me') ?>"></div>
    </div>

    <div class="dw-panel">
        <div class="dw-panel-head">
            <h2 class="dw-panel-title">Transaksi Terbaru</h2>
            <a href="<?= Router::url('transactions') ?>" class="dw-link">Lihat semua →</a>
        </div>

        <?php if (empty($recent)): ?>
            <p class="dw-empty">Belum ada transaksi. Mulai catat pemasukan atau pengeluaranmu.</p>
        <?php else: ?>
            <ul class="dw-ledger-list">
                <?php foreach ($recent as $t): ?>
                    <li class="dw-ledger-row">
                        <span class="dw-ledger-icon dw-ledger-icon-<?= $t['type'] ?>"><?= $t['type'] === 'masuk' ? '↓' : '↑' ?></span>
                        <div class="dw-ledger-info">
                            <span class="dw-ledger-desc"><?= View::e($t['description']) ?></span>
                            <span class="dw-ledger-date"><?= date('d M Y', strtotime($t['transaction_date'])) ?></span>
                        </div>
                        <span class="dw-ledger-amount dw-ledger-amount-<?= $t['type'] ?>">
                            <?= $t['type'] === 'masuk' ? '+' : '-' ?><?= View::rupiah($t['amount']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<script type="module" src="<?= Router::url('assets/js/app.js') ?>"></script>
