<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $daftarProduk
 * @var array<int, array<string, mixed>> $daftarKategori
 * @var string|null $keyword
 * @var string|null $kategoriId
 */
?>

<div class="katalog-page-wrapper">

    <header class="katalog-hero">
        <h1>Katalog Produk</h1>
        <p>Temukan barang impianmu dengan harga terbaik hari ini!</p>
    </header>

    <main class="container">

        <?php if (!empty($daftarKategori)): ?>
            <div class="visual-category-container">
                <h3 style="margin-top: 0px;">Kategori Pilihan</h3>
                <div class="category-scroll-wrapper">

                    <a href="<?= Helper::url('katalog') ?>" class="category-icon-item <?= empty($kategoriId) ? 'active' : '' ?>">
                        <div class="icon-circle icon-all">
                            <span>🛍️</span>
                        </div>
                        <span>Semua</span>
                    </a>

                    <?php foreach ($daftarKategori as $kat): ?>
                        <?php $isActive = ($kategoriId == $kat['id']) ? 'active' : ''; ?>
                        <a href="<?= Helper::url('katalog?kategori=' . $kat['id']) ?>" class="category-icon-item <?= $isActive ?>">
                            <div class="icon-circle">
                                <img src="<?= Helper::url(ltrim($kat['path_gambar'] ?? '/img/default-category.jpg', '/')) ?>" alt="<?= htmlspecialchars($kat['nama_kategori']) ?>">
                            </div>
                            <span><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                        </a>
                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($keyword) || !empty($kategoriId)): ?>
            <div class="search-indicator">
                Menampilkan hasil untuk <strong>"<?= htmlspecialchars($keyword ?: 'Semua') ?>"</strong>
                <a href="<?= Helper::url('katalog') ?>" class="text-danger btn-reset-search">✖ Reset Filter</a>
            </div>
        <?php endif; ?>

        <div class="katalog-container">
            <?php if (empty($daftarProduk)): ?>
                <div class="empty-state w-100">
                    <p>Maaf, produk yang Anda cari tidak ditemukan.</p>
                </div>
            <?php else: ?>
                <?php foreach ($daftarProduk as $produk): ?>
                    <?php require __DIR__ . '/partials/katalog_item.php'; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </main>

</div>
