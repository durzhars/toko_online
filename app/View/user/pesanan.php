<?php

use App\Core\Helper;

/** @var array<int, array<string, mixed>> $pesanan */
?>
<header class="page-header">
    <h1>Riwayat Pesanan Saya</h1>
    <p>Pantau status pesanan Anda di sini.</p>
</header>

<main class="container">
    <?php if (empty($pesanan)): ?>
        <div class="empty-state">
            <h2>Belum ada riwayat pesanan.</h2>
            <p>Silakan buat pesanan pertama Anda!</p><br>
            <a href="<?= Helper::url('katalog') ?>" class="btn btn-primary">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <?php require __DIR__ . '/../user/partials/tabel_pesanan.php'; ?>
    <?php endif; ?>
</main>
