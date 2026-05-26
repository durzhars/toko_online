<?php
/** @var \App\DTO\KatalogView $data */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data->title); ?></title>
    <link rel="stylesheet" href="/toko_online/public/css/katalog.css">
    <link rel="stylesheet" href="/toko_online/public/css/style.css">
</head>
<body>
    <header>
        <h1><?= htmlspecialchars($data->title); ?></h1>
        <p>Selamat datang! Berikut adalah produk unggulan kami.</p>
    </header>

    <main>
        <div class="katalog-container" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php if (empty($data->daftarProduk)): ?>
                <p>Belum ada produk yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($data->daftarProduk as $produk): ?>
                    <div class="kartu-produk" style="border: 1px solid #ccc; padding: 15px; width: 250px; border-radius: 8px;">
                        <h3><?= htmlspecialchars($produk['nama_produk']); ?></h3>
                        <p style="color: gray; font-size: 0.9em;">Kategori: <?= htmlspecialchars($produk['nama_kategori']); ?></p>
                        <p><strong>Harga:</strong> Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                        <p><strong>Stok:</strong> <?= htmlspecialchars($produk['stok']); ?></p>
                        <p style="font-size: 0.85em;"><?= htmlspecialchars($produk['deskripsi']); ?></p>
                        <form action="/toko_online/public/keranjang/tambah" method="POST">
                            <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">
                            <button type="submit" style="padding: 10px; width: 100%; cursor: pointer;">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
