<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $daftarAlamat
 * @var array<string, mixed> $alamat
 * @var int $index
 */
?><form action="<?= Helper::url('checkout/proses') ?>" method="POST" class="form-box">
    <h3 style="margin-top: 0;">Pilih Alamat Pengiriman</h3>
    <hr style="border-top: 1px solid #eee; margin-bottom: 20px;">

    <?php if (!empty($daftarAlamat)): ?>
        <?php foreach ($daftarAlamat as $index => $alamat): ?>
            <div class="form-group" style="padding: 15px; border: 1px solid <?= $index === 0 ? '#8bc34a' : '#ddd' ?>; border-radius: 5px; margin-bottom: 10px; background-color: <?= $index === 0 ? '#f1f8e9' : '#fff' ?>;">
                <label style="cursor: pointer; display: flex; align-items: flex-start; gap: 10px; width: 100%;">
                    <input type="radio" name="alamat_id" value="<?= $alamat['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> style="margin-top: 5px;">
                    <div>
                        <span class="badge badge-paid" style="margin-bottom: 5px; display: inline-block;">
                            <?= htmlspecialchars($alamat['label']) ?> <?= $alamat['is_utama'] ? '(Utama)' : '' ?>
                        </span><br>
                        <strong><?= htmlspecialchars($alamat['penerima']) ?></strong> (<?= htmlspecialchars($alamat['no_hp']) ?>)<br>
                        <span style="color: #555; font-size: 0.9em;"><?= nl2br(htmlspecialchars($alamat['alamat_lengkap'])) ?></span>
                    </div>
                </label>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="padding: 15px; background: #fff3cd; color: #33691e; border-radius: 5px; margin-bottom: 15px;">
            Anda belum memiliki alamat tersimpan. Silakan isi alamat pengiriman di bawah ini.
        </div>
    <?php endif; ?>

    <div class="form-group" style="padding: 15px; border: 1px dashed #999; border-radius: 5px; margin-top: 20px;">
        <label style="cursor: pointer; display: flex; align-items: center; gap: 10px; font-weight: bold; font-size: 1.1em;">
            <input type="radio" name="alamat_id" value="<?= $alamat['id'] ?>" <?= $index === 0 ? 'checked' : '' ?> class="radio-alamat-toggle" style="margin-top: 5px;">

            <input type="radio" name="alamat_id" value="baru" <?= empty($daftarAlamat) ? 'checked' : '' ?> class="radio-alamat-toggle">
            Kirim ke Alamat Lain (Satu Kali Transaksi)
        </label>

        <div id="ui-alamat-baru" style="display: <?= empty($daftarAlamat) ? 'block' : 'none' ?>; margin-top: 15px;">
            <div class="form-group">
                <label class="form-label">Nama Penerima</label>
                <input type="text" name="penerima_baru" class="form-control" placeholder="Nama penerima paket">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="no_hp_baru" class="form-control" placeholder="08123xxxx">
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat_lengkap_baru" rows="3" class="form-control" placeholder="Jl. Sudirman No 123..."></textarea>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success btn-block mt-30" data-confirm="Lanjutkan pesanan dengan detail pengiriman ini?">
        Konfirmasi & Buat Pesanan
    </button>
</form>
