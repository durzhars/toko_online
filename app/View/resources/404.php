<?php

use App\Core\Helper;

?>

<div class="empty-state" style="border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); background-color: #fff;">
    <h1 style="font-size: 6rem; margin: 25px; color: #ff4d4d;">404</h1>
    <h2 style="margin-top: 10px; margin-left: 25px; color: #333;">Halaman Tidak Ditemukan</h2>

    <p style="color: #666; font-size: 1.1rem; margin-left:25px;">
        Maaf, path URL <strong><?= htmlspecialchars($url ?? '/') ?></strong> yang Anda cari tidak tersedia di server ini.
    </p>

    <div style="display: flex; justify-content: center; gap: 15px; margin: 25px; padding-bottom: 25px;">
        <button onclick="window.history.back()" class="btn btn-dark">
            Kembali
        </button>

        <a href="<?= Helper::url('admin/dashboard') ?>" class="btn btn-primary">
            Ke Halaman Utama
        </a>
    </div>
</div>
