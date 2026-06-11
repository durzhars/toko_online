<?php

use App\Core\Helper;

?>

<div class="pesanan-container">
    <?php foreach ($pesanan as $trx): ?>
        <?php
        // Penentuan Warna Badge
        $badgeClass = match ($trx['status']) {
            'PAID' => 'badge-paid',
            'SHIPPED' => 'badge-shipped',
            'COMPLETED' => 'badge-completed',
            default => 'badge-pending',
        };

        // Ekstrak Array JSON Alamat
        $alamat = $trx['alamat_pengiriman'] ?? [];
        ?>

        <div class="pesanan-card">
            <div class="pesanan-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="pesanan-id">
                    <span style="font-size: 0.8em; color: #666;">No. Pesanan:</span><br>
                    <strong><?= htmlspecialchars($trx['id']) ?></strong>
                </div>
                <div class="pesanan-status" style="text-align: right;">
                    <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($trx['status']) ?></span>

                    <?php if ($trx['status'] === 'PENDING'): ?>
                        <div style="margin-top: 10px;">
                            <a href="<?= Helper::url('payment/' . $trx['id']) ?>" class="btn btn-success btn-sm">💳 Bayar Sekarang</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($trx['status'] === 'SHIPPED'): ?>
                        <div style="margin-top: 10px;">
                            <form action="<?= Helper::url('pesanan/selesai/' . $trx['id']) ?>" method="POST" style="margin: 0;">
                                <button type="submit" class="btn btn-primary btn-sm" data-confirm="Apakah Anda sudah menerima barang dengan baik? Status pesanan akan diselesaikan.">
                                    📦 Pesanan Diterima
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pesanan-body">
                <div class="pesanan-info">
                    <p class="pesanan-tanggal">📅 <?= date('d M Y, H:i', strtotime($trx['created_at'])) ?></p>
                    <p style="margin: 0; color: #555;">Total Tagihan:</p>
                    <p class="pesanan-total">Rp <?= number_format($trx['total_tagihan'], 0, ',', '.') ?></p>
                </div>

                <div class="pesanan-alamat">
                    <div style="margin-bottom: 8px;">
                        <span class="badge" style="background-color: #00bcd4; font-size: 0.7em;">
                            <?= htmlspecialchars($alamat['label'] ?? 'Alamat') ?>
                        </span>

                        <?php if (!empty($trx['resi_pengiriman'])): ?>
                            <span class="badge" style="background-color: #607d8b; font-size: 0.7em; margin-left: 5px;">
                                Resi: <?= htmlspecialchars($trx['resi_pengiriman']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="penerima">
                        <strong><?= htmlspecialchars($alamat['penerima'] ?? 'Tidak ada nama') ?></strong>
                        (<?= htmlspecialchars($alamat['no_hp'] ?? '-') ?>)
                    </p>
                    <p class="alamat-text">
                        <?= nl2br(htmlspecialchars($alamat['alamat_lengkap'] ?? 'Alamat tidak tersedia.')) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
