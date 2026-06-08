<?php

use App\Core\Helper;

?>

<div class="form-box">
    <h3 style="margin-top: 0; margin-bottom: 20px;">Informasi Pribadi</h3>

    <form action="<?= Helper::url('profil') ?>" method="POST">
        <input type="hidden" name="_method" value="PUT">

        <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text"
                name="nama"
                class="form-control"
                value="<?= htmlspecialchars($profil['nama'] ?? '') ?>"
                required>
        </div>

        <div class="form-group">
            <label class="form-label">Alamat Email</label>
            <input type="email"
                class="form-control"
                value="<?= htmlspecialchars($profil['email'] ?? '') ?>"
                disabled
                style="background-color: #f5f5f5; cursor: not-allowed;">
            <small style="color: #666; margin-top: 5px; display: block;">Email digunakan untuk login dan tidak dapat diubah.</small>
        </div>

        <div class="form-group">
            <label class="form-label">Nomor HP Utama</label>
            <input type="text"
                name="no_hp"
                class="form-control"
                value="<?= htmlspecialchars($profil['no_hp'] ?? '') ?>"
                placeholder="Contoh: 081234567890">
        </div>

        <button type="submit"
            class="btn btn-primary mt-10"
            data-confirm="Simpan perubahan informasi pribadi Anda?">
            Update Profil
        </button>
    </form>
</div>
