<?php

use App\Core\Helper;

/** @var array<int, array<string, mixed>> $kategori */
?>
<div class="d-flex justify-between" style="align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Daftar Kategori</h2>
    <a href="<?= Helper::url('admin/kategori/tambah') ?>" class="btn btn-success">+ Tambah Kategori Baru</a>
</div>

<table class="table-admin">
    <thead>
        <tr>

            <th width="50">ID</th>
            <th class="col-img">Gambar</th>
            <th>Nama Kategori</th>
            <th width="150" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($kategori)): ?>
            <tr>
                <td colspan="3" class="text-center">Belum ada data kategori.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($kategori as $kat): ?>
                <tr>
                    <td><?= $kat['id'] ?></td>
                    <td class="col-img">
                        <img src="<?= Helper::url(ltrim($kat['path_gambar'] ?? '/img/default-kategori.jpg', '/')) ?>"
                            alt="Foto"
                            class="img-table-thumb">
                    </td>
                    <td><?= htmlspecialchars($kat['nama_kategori']) ?></td>
                    <td class="text-center d-flex" style="gap: 5px; justify-content: center;">
                        <a href="<?= Helper::url('admin/kategori/' . $kat['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form action="<?= Helper::url('admin/kategori/' . $kat['id']) ?>" method="POST" style="margin: 0;">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger" data-confirm="Hapus kategori '<?= htmlspecialchars($kat['nama_kategori']) ?>'? Tindakan ini permanen.">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
