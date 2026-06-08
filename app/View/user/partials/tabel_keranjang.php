<?php

use App\Core\Helper;

/**
 * @var array<int, array<string, mixed>> $item
 * @var int|float $total
 */
?>
<table class="table-keranjang">
    <thead>
        <tr class="table-header">
            <th>No</th>
            <th>Nama Produk</th>
            <th>Harga Satuan</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($item as $produk): ?>
            <?php
            $hapusUrl = Helper::url('keranjang/hapus/' . $produk['id']);
            $namaProduk = htmlspecialchars($produk['nama_produk']);
            $hargaFmt = number_format($produk['harga'], 0, ',', '.');
            $subtotalFmt = number_format($produk['subtotal'], 0, ',', '.');
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $namaProduk; ?></td>
                <td>Rp <?= $hargaFmt; ?></td>
                <td><?= $produk['jumlah']; ?></td>
                <td><strong>Rp <?= $subtotalFmt; ?></strong></td>
                <td>
                    <a href="<?= $hapusUrl; ?>"
                        class="btn btn-danger btn-sm"
                        data-confirm="Yakin ingin menghapus produk dari keranjang?">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-right" style="padding-right: 20px;">Total Tagihan:</th>
            <th colspan="2" class="text-danger" style="font-size: 1.2em;">
                Rp <?= number_format($total, 0, ',', '.'); ?>
            </th>
        </tr>
    </tfoot>
</table>
