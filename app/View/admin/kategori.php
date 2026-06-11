<?php

use App\Core\Helper;

/** @var array<int, array<string, mixed>> $kategori */
?>

<div class="admin-header-actions" style="margin-bottom: 20px;">
    <h2 style="margin: 0;">Daftar Kategori</h2>
    <a href="<?= Helper::url('admin/kategori/tambah') ?>" class="btn btn-success">+ Tambah Kategori Baru</a>
</div>

<div class="table-responsive-box">
    <table class="table-admin">
        <thead>
            <tr>
                <th class="col-center" style="width: 60px;">ID</th>
                <th class="col-img">Gambar</th>
                <th>Nama Kategori</th>
                <th class="col-action" style="width: 150px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($kategori)): ?>
                <tr>
                    <td colspan="4" class="col-center" style="padding: 40px 20px; color: #888;">
                        <em>Belum ada data kategori di sistem.</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($kategori as $kat): ?>
                    <tr>
                        <td class="col-center"><strong><?= htmlspecialchars($kat['id']) ?></strong></td>

                        <td class="col-img">
                            <img src="<?= Helper::url(ltrim($kat['path_gambar'] ?? '/assets/brand-logo.jpg', '/')) ?>"
                                alt="Kategori"
                                class="img-table-thumb">
                        </td>

                        <td><?= htmlspecialchars($kat['nama_kategori']) ?></td>

                        <td class="col-action">
                            <a href="<?= Helper::url('admin/kategori/' . $kat['id'] . '/edit') ?>" class="btn btn-primary btn-sm">Edit</a>

                            <form action="<?= Helper::url('admin/kategori/' . $kat['id']) ?>" method="POST" class="form-inline form-ajax-delete">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus kategori '<?= htmlspecialchars($kat['nama_kategori']) ?>'? Tindakan ini permanen.">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
