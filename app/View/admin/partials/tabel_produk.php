<?php

use App\Core\Helper;

?>

<div class="table-responsive-box">
    <table class="table-admin">
        <thead>
            <tr>
                <th class="col-img">Gambar</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th class="col-center">Stok</th>
                <th class="col-action">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produk)): ?>
                <tr>
                    <td colspan="6" class="col-center" style="padding: 40px 20px; color: #888;">
                        <em>Belum ada data produk di sistem.</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($produk as $p): ?>
                    <tr>
                        <td class="col-img">
                            <img src="<?= Helper::url(ltrim($p['path_gambar'] ?? '/assets/brand-logo.jpg', '/')) ?>"
                                alt="Foto"
                                class="img-table-thumb">
                        </td>
                        <td><strong><?= htmlspecialchars($p['nama_produk']) ?></strong></td>
                        <td>
                            <span class="badge badge-paid">
                                <?= htmlspecialchars($p['nama_kategori'] ?? 'Tanpa Kategori') ?>
                            </span>
                        </td>
                        <td>Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                        <td class="col-center">
                            <?php if ($p['stok'] <= 5): ?>
                                <span style="color: #e53935; font-weight: bold;"><?= $p['stok'] ?></span>
                            <?php else: ?>
                                <?= $p['stok'] ?>
                            <?php endif; ?>
                        </td>
                        <td class="col-action">
                            <a href="<?= Helper::url('admin/produk/' . $p['id'] . '/edit') ?>" class="btn btn-primary btn-sm">Edit</a>

                            <form action="<?= Helper::url('admin/produk/' . $p['id']) ?>" method="POST" class="form-inline">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Yakin ingin menghapus produk ini?">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
