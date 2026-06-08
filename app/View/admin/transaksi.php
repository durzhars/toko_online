<?php

use App\Core\Helper;

?>

<div class="admin-header-actions">
    <h2>Daftar Pesanan Masuk</h2>
</div>

<div class="table-responsive-box">
    <table class="table-admin">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total Tagihan</th>
                <th class="col-center">Status</th>
                <th class="col-action">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pesanan)): ?>
                <tr>
                    <td colspan="6" class="col-center" style="padding: 40px 20px; color: #888;">
                        <em>Belum ada pesanan masuk.</em>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pesanan as $trx): ?>
                    <?php
                    $badgeClass = match ($trx['status']) {
                        'PAID' => 'badge-paid',
                        'SHIPPED' => 'badge-shipped',
                        'COMPLETED' => 'badge-completed',
                        default => 'badge-pending',
                    };
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($trx['id']) ?></strong></td>
                        <td><?= date('d M Y, H:i', strtotime($trx['created_at'])) ?></td>
                        <td><?= htmlspecialchars($trx['nama_pelanggan']) ?></td>
                        <td>Rp <?= number_format($trx['total_tagihan'], 0, ',', '.') ?></td>
                        <td class="col-center">
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($trx['status']) ?></span>
                        </td>
                        <td class="col-action">
                            <a href="<?= Helper::url('admin/pesanan/' . $trx['id']) ?>" class="btn btn-primary btn-sm">Proses / Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
