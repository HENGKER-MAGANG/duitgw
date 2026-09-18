<?php
use App\Core\Router;
use App\Core\View;
use App\Core\Upload;
$title = 'Edit Transaksi';
$active = 'transactions';
?>

<h1 class="dw-page-title">Edit Transaksi</h1>

<?php if (!empty($error)): ?>
    <div class="dw-alert dw-alert-error"><?= View::e($error) ?></div>
<?php endif; ?>

<div class="dw-panel dw-panel-form">
    <div id="react-transaction-form"
         data-action="<?= Router::url('transactions/' . $transaction['id']) ?>"
         data-redirect="<?= Router::url('transactions') ?>"
         data-mode="edit"
         data-categories='<?= json_encode(array_map(fn($c) => ['name' => $c['name'], 'type' => $c['type']], $categories), JSON_UNESCAPED_UNICODE) ?>'
         data-transaction='<?= json_encode([
             'type' => $transaction['type'],
             'amount' => $transaction['amount'],
             'description' => $transaction['description'],
             'transaction_date' => $transaction['transaction_date'],
             'proof_url' => Upload::url($transaction['proof_photo']),
         ], JSON_UNESCAPED_UNICODE) ?>'>
    </div>
</div>

<script type="module" src="<?= Router::url('assets/js/app.js') ?>"></script>
