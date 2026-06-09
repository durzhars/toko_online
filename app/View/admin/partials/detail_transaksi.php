<?php

use App\Core\Helper;

$alamat = $pesanan['alamat_pengiriman'] ?? [];
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
                            <td style="text-align: right;">Rp <?= number_format($item['harga_satuan'] * $item['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="flex: 1; min-width: 300px;">
        <div class="form-box" style="background-color: #f8f9fa;">
            <h3 style="margin-top: 0;">Aksi Admin</h3>
            <hr>
            <form action="<?= Helper::url('admin/pesanan/' . $pesanan['id']) ?>" method="POST">
                <input type="hidden" name="_method" value="PUT">

                <div class="form-group">
                    <label class="form-label">Status Pesanan</label>
                    <select name="status" class="form-control">
                        <option value="PENDING" <?= $pesanan['status'] == 'PENDING' ? 'selected' : '' ?>>PENDING (Menunggu Pembayaran)</option>
                        <option value="PAID" <?= $pesanan['status'] == 'PAID' ? 'selected' : '' ?>>PAID (Sudah Dibayar)</option>
                        <option value="SHIPPED" <?= $pesanan['status'] == 'SHIPPED' ? 'selected' : '' ?>>SHIPPED (Dikirim)</option>
                        <option value="COMPLETED" <?= $pesanan['status'] == 'COMPLETED' ? 'selected' : '' ?>>COMPLETED (Selesai)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Resi Pengiriman</label>
                    <input type="text" name="resi_pengiriman" class="form-control" placeholder="Input resi kurir..." value="<?= htmlspecialchars($pesanan['resi_pengiriman'] ?? '') ?>">
                </div>

                <button type="submit" class="btn btn-success btn-block" data-confirm="Simpan pembaruan status dan resi?">
                    Simpan Pembaruan
                </button>
            </form>
        </div>
    </div>
</div>
