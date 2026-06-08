<?php

/**
 * @var array<int, array<string, mixed>> $item
 * @var int|float $total
 */
?>
<div class="summary-box">
    <h3>Ringkasan Pesanan</h3>
    <hr>
    <ul class="summary-list">
        <?php foreach ($item as $produk): ?>
            <li class="summary-item">
                <span><?= $produk['jumlah'] ?>x <?= htmlspecialchars($produk['nama_produk']) ?></span>
                <span>Rp <?= number_format($produk['subtotal'], 0, ',', '.') ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
    <hr>
    <div class="d-flex justify-between text-danger" style="font-size: 1.2em; font-weight: bold;">
        <span>Total Tagihan:</span>
        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
    </div>
</div>
