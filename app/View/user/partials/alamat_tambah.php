<?php

use App\Core\Helper;

?>

<header class="page-header">
    <h1>Tambah Alamat Baru</h1>
    <p>Simpan alamat untuk mempermudah proses checkout Anda nantinya.</p>
</header>

<main class="container-sm">
    <div class="form-box">
        <form action="<?= Helper::url('profil/alamat') ?>" method="POST">

            <div class="form-group">
                <label class="form-label">Label Alamat (Opsional)</label>
                <input type="text" name="label" class="form-control" placeholder="Contoh: Rumah, Kantor, Kosan Budi" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="penerima" class="form-control" placeholder="Nama lengkap penerima paket" required>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor HP Penerima</label>
                <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789" required>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" class="form-control" rows="4" placeholder="Nama Jalan, Gedung, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos" required></textarea>
            </div>

            <div class="form-group" style="margin-top: 20px;">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="is_utama" value="1" style="width: 20px; height: 20px;">
                    <strong>Jadikan sebagai Alamat Utama</strong>
                </label>
            </div>

            <div class="d-flex" style="gap: 10px; margin-top: 30px;">
                <a href="<?= Helper::url('profil') ?>" class="btn btn-dark" style="flex: 1;">Batal</a>
                <button type="submit" class="btn btn-success" style="flex: 2;" data-confirm="Simpan alamat baru ini?">Simpan Alamat</button>
            </div>
        </form>
    </div>
</main>
