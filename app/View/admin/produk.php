<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $produk
 */
?>

<div class="admin-header-actions">
    <h2>Manajemen Produk</h2>
    <a href="<?= Helper::url('admin/produk/tambah') ?>" class="btn btn-success">+ Tambah Produk</a>
</div>

<?php require __DIR__ . '/../admin/partials/tabel_produk.php'; ?>
