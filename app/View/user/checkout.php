<?php

/**
 * @var array<int, array<string, mixed>> $item
 * @var int|float $total
 * @var array<int, array<string, mixed>> $daftarAlamat
 */
?>
<header class="page-header">
    <h1>Checkout Pesanan</h1>
    <p>Lengkapi detail pengiriman Anda.</p>
</header>

<main class="container-sm">
    <?php require __DIR__ . '/../user/partials/checkout_summary.php'; ?>

    <?php require __DIR__ . '/../user/partials/checkout_form.php'; ?>
</main>
