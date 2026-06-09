<?php

use App\Core\Helper;

?>
<div class="d-flex justify-between" style="align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0;">Tambah Kategori Baru</h2>
    <a href="<?= Helper::url('admin/kategori') ?>" class="btn btn-dark btn-sm">Batal / Kembali</a>
</div>

<div class="form-box" style="max-width: 500px;">
    <form action="<?= Helper::url('admin/kategori') ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required placeholder="Contoh: Laptop, Pakaian, Makanan...">
        </div>
        <div class="form-group">
            <label class="form-label">Ikon / Gambar Kategori</label><br>
            <img src="<?= \App\Core\Helper::url(ltrim($kategori['path_gambar'] ?? '/assets/brand-logo.jpg', '/')) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; border: 1px solid #ddd;">
            <input type="file" name="path_gambar" class="form-control" accept="image/png, image/jpeg, image/webp">
        </div>
        <button type="submit" class="btn btn-primary mt-10">Simpan Kategori</button>
    </form>
</div>
