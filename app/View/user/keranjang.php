<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $item
 * @var int|float $total
 */
?>
<header class="page-header">
    <h1>Keranjang Belanja</h1>
    <p>Periksa kembali barang belanjaan Anda sebelum melakukan pembayaran.</p>
</header>

<main class="container">
    <?php if (empty($item)): ?>
        <div class="empty-state">
            <h2>Keranjang Anda masih kosong 🛒</h2>
            <p>Silakan lihat-lihat katalog produk kami terlebih dahulu.</p><br>
            <a href="<?= Helper::url('katalog') ?>" class="btn btn-primary">Kembali Belanja</a>
        </div>
    <?php else: ?>
        <?php require __DIR__ . '/../user/partials/tabel_keranjang.php'; ?>

        <div class="text-right mt-30">
            <a href="<?= Helper::url('katalog') ?>" class="btn btn-primary ml-10">Lanjut Belanja</a>
            <a href="<?= Helper::url('checkout') ?>" class="btn btn-success ml-10">Lanjut ke Pembayaran ➔</a>
        </div>
    <?php endif; ?>
</main>
