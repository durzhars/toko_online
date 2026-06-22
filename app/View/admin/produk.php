<?php

use App\Core\Helper;

?>

<div class="admin-header-actions" style="margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
    <h2 style="margin: 0;">Daftar Produk</h2>

    <div style="display: flex; gap: 10px; flex: 1; justify-content: flex-end;">
        <form action="<?= Helper::url('admin/produk') ?>" method="GET" style="display: flex; gap: 10px; margin: 0; min-width: 250px;">
            <input type="search" name="q" class="form-control" style="width: auto;" placeholder="Cari nama atau kategori..." value="<?= htmlspecialchars($keyword ?? '') ?>">
            <button type="submit" class="btn btn-primary">🔍 Cari</button>

            <?php if (!empty($keyword)): ?>
                <a href="<?= Helper::url('admin/produk') ?>" class="btn btn-dark" title="Hapus Pencarian">✖</a>
            <?php endif; ?>
        </form>

        <a href="<?= Helper::url('admin/produk/tambah') ?>" class="btn btn-success">+ Tambah Produk</a>
    </div>
</div>

<?php require __DIR__ . '/../admin/partials/tabel_produk.php'; ?>
