<?php
use App\Core\Router;
use App\Core\View;
$title = 'Catatan Keuangan';
$active = 'transactions';
?>

<?php if (!empty($success)): ?>
    <div class="dw-alert dw-alert-success"><?= View::e($success) ?></div>
<?php endif; ?>

<div class="dw-panel-head">
    <h1 class="dw-page-title">Buku Kas</h1>
    <a href="<?= Router::url('transactions/create') ?>" class="dw-btn dw-btn-primary">＋ Catat Transaksi</a>
</div>

<form method="GET" action="<?= Router::url('transactions') ?>" class="dw-filter-bar">
    <select name="type" class="dw-select">
        <option value="">Semua Jenis</option>
        <option value="masuk" <?= ($filters['type'] ?? '') === 'masuk' ? 'selected' : '' ?>>Pemasukan</option>
        <option value="keluar" <?= ($filters['type'] ?? '') === 'keluar' ? 'selected' : '' ?>>Pengeluaran</option>
    </select>
    <input type="month" name="month" class="dw-input" value="<?= View::e($filters['month'] ?? '') ?>">
    <input type="text" name="search" class="dw-input" placeholder="Cari keterangan..." value="<?= View::e($filters['search'] ?? '') ?>">
    <button type="submit" class="dw-btn dw-btn-outline">Saring</button>
</form>

<?php if (empty($transactions)): ?>
    <div class="dw-panel dw-empty-state">
        <span class="dw-empty-icon">🧾</span>
        <p>Belum ada catatan yang cocok. Ayo mulai catat transaksi pertamamu.</p>
        <a href="<?= Router::url('transactions/create') ?>" class="dw-btn dw-btn-primary">Catat Transaksi</a>
    </div>
<?php else: ?>
    <div class="dw-receipt-table">
        <?php foreach ($transactions as $t): ?>
            <div class="dw-receipt-row">
                <img class="dw-proof-thumb" src="<?= \App\Core\Upload::url($t['proof_photo']) ?>" alt="Bukti foto"
                     onclick="dwOpenProof('<?= \App\Core\Upload::url($t['proof_photo']) ?>')">
                <div class="dw-receipt-main">
                    <span class="dw-receipt-desc"><?= View::e($t['description']) ?></span>
                    <span class="dw-receipt-meta">
                        <?= date('d M Y', strtotime($t['transaction_date'])) ?>
                        <?= $t['category_name'] ? '· ' . View::e($t['category_name']) : '' ?>
                    </span>
                </div>
                <span class="dw-receipt-amount dw-ledger-amount-<?= $t['type'] ?>">
                    <?= $t['type'] === 'masuk' ? '+' : '-' ?><?= View::rupiah($t['amount']) ?>
                </span>
                <div class="dw-receipt-actions">
                    <a href="<?= Router::url('transactions/' . $t['id'] . '/edit') ?>" class="dw-icon-btn" title="Edit">✎</a>
                    <form action="<?= Router::url('transactions/' . $t['id'] . '/delete') ?>" method="POST"
                          onsubmit="return confirm('Hapus transaksi ini?');">
                        <button type="submit" class="dw-icon-btn dw-icon-btn-danger" title="Hapus">🗑</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="dw-proof-modal" id="dwProofModal" onclick="dwCloseProof()">
    <img id="dwProofModalImg" src="" alt="Bukti foto transaksi">
</div>
<script>
function dwOpenProof(src) {
    document.getElementById('dwProofModalImg').src = src;
    document.getElementById('dwProofModal').classList.add('is-open');
}
function dwCloseProof() {
    document.getElementById('dwProofModal').classList.remove('is-open');
}
</script>
