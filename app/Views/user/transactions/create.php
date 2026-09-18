<?php
use App\Core\Router;
use App\Core\View;
$title = 'Catat Transaksi';
$active = 'create';
?>

<h1 class="dw-page-title">Catat Transaksi Baru</h1>
<p class="dw-page-subtitle">Setiap transaksi wajib dilampiri bukti foto (struk, nota, atau screenshot transfer).</p>

<?php if (!empty($error)): ?>
    <div class="dw-alert dw-alert-error"><?= View::e($error) ?></div>
<?php endif; ?>

<div class="dw-panel dw-panel-form">
    <div id="react-transaction-form"
         data-action="<?= Router::url('transactions') ?>"
         data-redirect="<?= Router::url('transactions') ?>"
         data-categories='<?= json_encode(array_map(fn($c) => ['name' => $c['name'], 'type' => $c['type']], $categories), JSON_UNESCAPED_UNICODE) ?>'>
    </div>
</div>

<noscript>
    <p class="dw-alert dw-alert-error">Formulir ini membutuhkan JavaScript aktif di browser kamu.</p>
</noscript>

<script type="module" src="<?= Router::url('assets/js/app.js') ?>"></script>
