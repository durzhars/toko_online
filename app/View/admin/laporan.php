<?php

use App\Core\Helper;

?>

<div class="admin-header-actions print-hidden">
    <h2>Laporan Penjualan</h2>
</div>

<div class="form-box mb-20 print-hidden" style="margin-bottom: 20px;">
    <form action="<?= Helper::url('admin/laporan') ?>" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

        <div style="flex: 1; min-width: 150px;">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label class="form-label">Tanggal Sampai</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label class="form-label">Status Transaksi</label>
            <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <option value="PAID" <?= $status === 'PAID' ? 'selected' : '' ?>>PAID (Lunas)</option>
                <option value="SHIPPED" <?= $status === 'SHIPPED' ? 'selected' : '' ?>>SHIPPED (Dikirim)</option>
                <option value="COMPLETED" <?= $status === 'COMPLETED' ? 'selected' : '' ?>>COMPLETED (Selesai)</option>
                <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>PENDING (Belum Bayar)</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary" style="height: 42px;">Filter Tampilkan</button>

            <button type="submit" formaction="<?= Helper::url('admin/laporan/export') ?>" class="btn btn-success" style="height: 42px;">📥 Ekspor CSV</button>

            <button type="button" onclick="window.print()" class="btn btn-dark" style="height: 42px;">🖨️ Cetak</button>
        </div>
    </form>
</div>

<div class="print-only" style="display: none; text-align: center; margin-bottom: 20px;">
    <h2>Laporan Penjualan QShop</h2>
    <p>Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></p>
</div>

<div class="stat-cards-container">
    <div class="stat-card" style="border-left-color: #4CAF50;">
        <h3>Total Pendapatan Terverifikasi</h3>
        <p class="stat-value text-success" style="color: #2e7d32;">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
        <small style="color: #888;">(Dari transaksi PAID, SHIPPED, COMPLETED)</small>
    </div>
    <div class="stat-card">
        <h3>Total Transaksi</h3>
        <p class="stat-value"><?= count($laporan) ?></p>
        <small style="color: #888;">(Berdasarkan filter aktif)</small>
    </div>
</div>

<div class="table-responsive-box">
    <table class="table-admin">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal & Waktu</th>
                <th>Nama Pelanggan</th>
                <th class="col-center">Status</th>
                <th style="text-align: right;">Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($laporan)): ?>
                <tr>
                    <td colspan="5" class="col-center" style="padding: 30px; color: #888;">Tidak ada data pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($laporan as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['id']) ?></strong></td>
                        <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                        <td class="col-center">
                            <span class="badge badge-<?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp <?= number_format($row['total_tagihan'], 0, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
