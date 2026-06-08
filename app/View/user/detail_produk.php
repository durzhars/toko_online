<?php

use App\Core\Helper;

/**
 * @var array<string, mixed> $produk
 * @var array<int, array<string, mixed>> $galeri
 * @var array<int, array<string, mixed>> $rekomendasi
 */
$gambarUtama = Helper::url(ltrim($produk['path_gambar'] ?? '/img/default-product.jpg', '/'));
?>

<main class="container detail-page-container">

    <div class="unified-card">
        <div class="detail-wrapper">

            <div class="detail-gallery">
                <div class="main-image-box">
                    <img src="<?= $gambarUtama ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" id="mainProductImage">
                </div>

                <div class="gallery-thumbnails">
                    <img src="<?= $gambarUtama ?>" class="thumb-item active" onclick="changeMainImage(this.src, this)">
                    <?php if (!empty($galeri)): ?>
                        <?php foreach ($galeri as $foto): ?>
                            <img src="<?= Helper::url(ltrim($foto['path_gambar'], '/')) ?>" class="thumb-item" onclick="changeMainImage(this.src, this)">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="detail-info">
                <span class="category-label">
                    <?= htmlspecialchars($produk['nama_kategori'] ?? 'Kategori') ?>
                </span>

                <h1 class="detail-title"><?= htmlspecialchars($produk['nama_produk']) ?></h1>

                <div class="detail-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>

                <div class="detail-meta">
                    Stok Tersedia: <strong><?= $produk['stok'] ?></strong> barang
                </div>

                <div class="detail-description-content">
                    <?= nl2br(htmlspecialchars($produk['deskripsi'] ?? '')) ?>
                </div>

                <hr class="detail-divider">

                <form action="<?= Helper::url('keranjang/tambah') ?>" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                    <div class="qty-control">
                        <input type="number" name="jumlah" class="form-control qty-input" value="1" min="1" max="<?= $produk['stok'] ?>" required <?= $produk['stok'] <= 0 ? 'disabled' : '' ?>>
                    </div>

                    <button type="submit" class="btn btn-primary btn-add-cart" <?= $produk['stok'] <= 0 ? 'disabled' : '' ?>>
                        <?= $produk['stok'] <= 0 ? 'Stok Habis' : '🛒 Masukkan Keranjang' ?>
                    </button>
                </form>

                <div class="sticky-cart-footer">
                    <button type="button" class="btn btn-primary btn-block btn-sticky" onclick="document.querySelector('.add-to-cart-form').submit()">
                        🛒 Tambah ke Keranjang
                    </button>
                </div>
            </div>

        </div>
    </div>

    <?php if (!empty($rekomendasi)): ?>
        <div class="recommendation-section">
            <h3 class="recommendation-title">Produk Lain di Kategori Ini</h3>
            <div class="katalog-container">
                <?php foreach ($rekomendasi as $item): ?>
                    <?php
                    // Overwrite variabel $produk agar partials katalog_item bisa membacanya
                    $produk = $item;
                    require __DIR__ . '/partials/katalog_item.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</main>

<script>
    function changeMainImage(src, element) {
        document.getElementById('mainProductImage').src = src;
        let thumbs = document.querySelectorAll('.thumb-item');
        thumbs.forEach(thumb => thumb.classList.remove('active'));
        element.classList.add('active');
    }
</script>
