<?php

use App\Core\Helper;

/**
 * Partial untuk Kartu Item Katalog.
 * @var array<string, mixed> $produk
 */
?>
<div class="product-card">
    <a href="<?= Helper::url('katalog/' . $produk['id']) ?>" class="product-link">
        <div class="product-image-wrapper">
            <img src="<?= Helper::url(ltrim($produk['path_gambar'] ?? '/assets/logo.png', '/')) ?>"
                alt="<?= htmlspecialchars($produk['nama_produk']) ?>"
                class="product-thumb">
            <?php if ($produk['stok'] <= 0): ?>
                <span class="badge-stock-out">Habis</span>
            <?php endif; ?>
        </div>

        <div class="product-info">
            <span class="category-label"><?= htmlspecialchars($produk["nama_kategori"] ?? 'Umum') ?></span>
            <h3 class="product-title" title="<?= htmlspecialchars($produk["nama_produk"]) ?>">
                <?= htmlspecialchars($produk["nama_produk"]) ?>
            </h3>
            <div class="product-price">
                Rp <?= number_format($produk["harga"], 0, ",", ".") ?>
            </div>
            <div class="product-stock-info">
                Tersisa <?= htmlspecialchars($produk["stok"]) ?> stok
            </div>
        </div>
    </a>

    <div class="product-action">
        <form action="<?= Helper::url('keranjang/tambah') ?>" method="POST" class="form-tambah">
            <input type="hidden" name="produk_id" value="<?= $produk["id"] ?>">
            <input type="hidden" name="jumlah" value="1">
            <button type="submit" class="btn btn-primary btn-block btn-cart" <?= $produk['stok'] <= 0 ? 'disabled' : '' ?>>
                🛒 Tambah
            </button>
        </form>
    </div>
</div>
