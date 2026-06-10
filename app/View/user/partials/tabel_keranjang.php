<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $item
 * @var int|float $total
 */
?>

<form action="<?= Helper::url('keranjang/batch-hapus') ?>" method="POST" id="form-batch-hapus"></form>

<div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
    <button type="submit" form="form-batch-hapus" class="btn btn-danger btn-sm" id="btnBatchDelete" disabled data-confirm="Yakin ingin menghapus semua produk yang dipilih dari keranjang?">
        🗑️ Hapus Terpilih
    </button>
    <span style="color: #666; font-size: 0.9rem;">Centang produk yang ingin dikelola</span>
</div>

<div class="table-responsive-box">
    <table class="table-keranjang">
        <thead>
            <tr class="table-header">
                <th style="width: 50px; text-align: center;">
                    <input type="checkbox" id="selectAllCb" style="transform: scale(1.2); cursor: pointer;">
                </th>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: right;">Subtotal</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($item as $produk): ?>
                <?php
                $hapusUrl = Helper::url('keranjang/hapus/' . $produk['id']);
                $namaProduk = htmlspecialchars($produk['nama_produk']);
                $hargaFmt = number_format($produk['harga'], 0, ',', '.');
                $subtotalFmt = number_format($produk['subtotal'], 0, ',', '.');
                $gambarProduk = Helper::url(ltrim($produk['path_gambar'] ?? '/img/default-product.jpg', '/'));
                ?>
                <tr>
                    <td style="text-align: center;">
                        <input type="checkbox" name="hapus_ids[]" value="<?= $produk['id'] ?>" form="form-batch-hapus" class="item-checkbox" style="transform: scale(1.2); cursor: pointer;">
                    </td>

                    <td>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="<?= $gambarProduk ?>" alt="<?= $namaProduk ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                            <strong><?= $namaProduk; ?></strong>
                        </div>
                    </td>

                    <td>Rp <?= $hargaFmt; ?></td>

                    <td style="text-align: center; font-weight: bold;">
                        <?= $produk['jumlah']; ?>
                    </td>

                    <td style="text-align: right; color: #e53935; font-weight: bold;">
                        Rp <?= $subtotalFmt; ?>
                    </td>

                    <td style="text-align: center;">
                        <a href="<?= $hapusUrl; ?>" class="btn btn-danger btn-sm" data-confirm="Hapus <?= $namaProduk ?> dari keranjang?" style="padding: 4px 8px; font-size: 0.85rem;">
                            🗑️ Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #fcfcfc;">
                <th colspan="4" class="text-right" style="padding: 20px;">Total Tagihan:</th>
                <th colspan="2" class="text-danger" style="font-size: 1.3rem; padding: 20px; text-align: right;">
                    Rp <?= number_format($total, 0, ',', '.'); ?>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
