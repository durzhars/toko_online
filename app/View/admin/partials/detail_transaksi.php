<?php

use App\Core\Helper;

/** @var array<string, mixed> $pesanan */

$alamat = $pesanan['alamat_pengiriman'] ?? [];

// Tentukan apakah Admin boleh mengubah resi (Hanya jika pesanan sudah dibayar atau sedang dikirim)
$isEditable = in_array($pesanan['status'], ['PAID']);
?>

<div class="admin-header-actions">
    <h2>Proses Pesanan: <?= htmlspecialchars($pesanan['id']) ?></h2>
    <a href="<?= Helper::url('admin/pesanan') ?>" class="btn btn-dark">Kembali</a>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <div style="flex: 2; min-width: 400px;">
        <div class="form-box">
            <h3 style="margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px;">Informasi Pengiriman</h3>
            <p><strong>Penerima:</strong> <?= htmlspecialchars($alamat['penerima'] ?? '-') ?></p>
            <p><strong>No. HP:</strong> <?= htmlspecialchars($alamat['no_hp'] ?? '-') ?></p>
            <p><strong>Alamat:</strong> <br><?= nl2br(htmlspecialchars($alamat['alamat_lengkap'] ?? '-')) ?></p>
        </div>

        <div class="form-box mt-30" style="padding: 0;">
            <table class="table-admin">
                <thead>
                    <tr>
                        <th class="col-img">Produk</th>
                        <th>Nama</th>
                        <th class="col-center">Qty</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="col-img">
                                <img src="<?= Helper::url(ltrim($item['path_gambar'] ?? '/assets/brand-logo.jpg', '/')) ?>" class="img-table-thumb" alt="thumb">
                            </td>
                            <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                            <td class="col-center"><?= $item['jumlah'] ?></td>
                            <td style="text-align: right;">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="flex: 1; min-width: 300px;">
        <div class="form-box" style="background-color: #f8f9fa;">
            <h3 style="margin-top: 0;">Status & Pengiriman</h3>
            <hr>

            <div style="margin-bottom: 20px; text-align: center;">
                <span style="font-size: 0.9em; color: #666;">Status Saat Ini:</span><br>
                <span class="badge badge-<?= strtolower($pesanan['status']) ?>" style="font-size: 1.2em; padding: 10px 15px; margin-top: 5px; display: inline-block;">
                    <?= htmlspecialchars($pesanan['status']) ?>
                </span>
            </div>

            <?php if ($pesanan['status'] === 'PENDING'): ?>
                <div class="alert alert-warning" style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; font-size: 0.9em;">
                    ⏳ Menunggu pelanggan menyelesaikan pembayaran. Anda belum bisa memproses pengiriman.
                </div>
            <?php elseif ($pesanan['status'] === 'COMPLETED'): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; font-size: 0.9em;">
                    ✅ Pesanan telah selesai dan diterima oleh pelanggan. Tidak ada aksi lebih lanjut.
                </div>
            <?php endif; ?>

            <form action="<?= Helper::url('admin/pesanan/' . $pesanan['id']) ?>" method="POST" style="<?= !$isEditable ? 'opacity: 0.6; pointer-events: none;' : '' ?>">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="status" value="SHIPPED">

                <div class="form-group">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" name="resi_pengiriman" class="form-control" placeholder="Input resi kurir..." value="<?= htmlspecialchars($pesanan['resi_pengiriman'] ?? '') ?>" <?= !$isEditable ? 'disabled' : 'required' ?>>
                    <small style="color: #666; display: block; margin-top: 5px;">Menyimpan resi akan mengubah status pesanan menjadi <b>SHIPPED</b>.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block" data-confirm="Kirimkan pesanan ini sekarang?" <?= !$isEditable ? 'disabled' : '' ?>>
                    🚀 Proses Pengiriman
                </button>
            </form>
        </div>
    </div>
</div>
