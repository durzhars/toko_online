<?php

use App\Core\Helper;

?>

<div class="d-flex justify-between" style="align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Tambah Produk Baru</h2>
    <a href="<?= Helper::url('admin/produk') ?>" class="btn btn-dark btn-sm">Kembali</a>
</div>

<div class="form-box">
    <form action="<?= Helper::url('admin/produk') ?>" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" required>
        </div>

        <div class="form-group">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= $kat['id'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="d-flex" style="gap: 20px;">
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" min="0" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label class="form-label">Stok Fisik</label>
                <input type="number" name="stok" class="form-control" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi Produk</label>
            <textarea name="deskripsi" class="form-control" rows="5"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Upload Foto Produk (Opsional)</label>
            <input type="file" name="path_gambar" class="form-control" accept="image/png, image/jpeg, image/webp" style="padding: 7px;">
            <small style="color: #666;">Format yang didukung: JPG, PNG, WEBP.</small>
        </div>

        <button type="submit" class="btn btn-success mt-10">Simpan Produk</button>
    </form>
</div>
