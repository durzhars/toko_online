<?php

use App\Core\Helper;

?>

<div class="d-flex justify-between" style="align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Edit Produk</h2>
    <a href="<?= Helper::url('admin/produk') ?>" class="btn btn-dark btn-sm">Kembali</a>
</div>

<div class="form-box">
    <form action="<?= Helper::url('admin/produk/' . $produk['id']) ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_method" value="PUT">

        <div class="form-group">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-control" required>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= $kat['id'] ?>" <?= $kat['id'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex" style="gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="<?= $produk['harga'] ?>" min="0" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Stok Fisik</label>
                <input type="number" name="stok" class="form-control" value="<?= $produk['stok'] ?>" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Produk</label>
            <textarea name="deskripsi" class="form-control" rows="5"><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Ganti Foto Produk (Biarkan kosong jika tidak ingin mengubah)</label><br>
            <img src="<?= Helper::url(ltrim($produk['path_gambar'] ?? '/assets/brand-logo.jpg', '/')) ?>" alt="Foto saat ini" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; border: 1px solid #ddd;">
            <input type="file" name="path_gambar" class="form-control" accept="image/png, image/jpeg, image/webp" style="padding: 7px;">
        </div>

        <button type="submit" class="btn btn-primary mt-10" data-confirm="Simpan perubahan produk ini?">Perbarui Produk</button>
    </form>
</div>
