<?php

use App\Core\Helper;

?>

<div class="form-box mt-30">
    <div class="d-flex justify-between" style="align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0;">Buku Alamat</h3>
        <a href="<?= Helper::url('profil/alamat/tambah') ?>" class="btn btn-success btn-sm">+ Tambah Alamat</a>
    </div>

    <?php if (empty($daftarAlamat)): ?>
        <div style="padding: 15px; background: #fff3cd; color: #33691e; border-radius: 5px;">
            Anda belum memiliki alamat yang tersimpan.
        </div>
    <?php else: ?>
        <?php foreach ($daftarAlamat as $alamat): ?>
            <div style="padding: 15px; border: 1px solid <?= $alamat['is_utama'] ? '#8bc34a' : '#ddd' ?>; border-radius: 5px; margin-bottom: 10px; background-color: <?= $alamat['is_utama'] ? '#f1f8e9' : '#fff' ?>;">

                <div class="d-flex justify-between">
                    <div>
                        <span class="badge <?= $alamat['is_utama'] ? 'badge-paid' : 'badge-pending' ?>" style="margin-bottom: 5px; display: inline-block;">
                            <?= htmlspecialchars($alamat['label']) ?> <?= $alamat['is_utama'] ? '(Utama)' : '' ?>
                        </span><br>
                        <strong><?= htmlspecialchars($alamat['penerima']) ?></strong> (<?= htmlspecialchars($alamat['no_hp']) ?>)<br>
                        <span style="color: #555; font-size: 0.9em;"><?= nl2br(htmlspecialchars($alamat['alamat_lengkap'])) ?></span>
                    </div>

                    <div style="text-align: right;">
                        <?php if (!$alamat['is_utama']): ?>
                            <form action="<?= Helper::url('profil/alamat/' . $alamat['id'] . '/utama') ?>" method="POST" style="display:inline;">
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" class="btn btn-primary btn-sm" style="margin-right: 5px;">Jadikan Utama</button>
                            </form>
                        <?php endif; ?>

                        <form action="<?= Helper::url('profil/alamat/' . $alamat['id']) ?>" method="POST" style="display:inline;">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus alamat ini dari buku alamat?">Hapus</button>
                        </form>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
