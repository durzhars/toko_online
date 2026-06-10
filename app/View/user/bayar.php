<?php

use App\Core\Helper;

/**
 * @var array<string, mixed> $pesanan
 */
?>

<main class="container-sm" style="margin-top: 50px; margin-bottom: 50px; max-width: 600px;">

    <div class="form-box" style="border-top: 5px solid #2196F3; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h2 style="margin: 0; color: #333;">Kyushop Secure Payment</h2>
            <p style="color: #666; margin-top: 5px;">Selesaikan pembayaran Anda dalam batas waktu yang ditentukan.</p>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px dashed #ccc;">
            <div class="d-flex justify-between" style="margin-bottom: 10px;">
                <span style="color: #555;">ID Pesanan</span>
                <strong><?= htmlspecialchars($pesanan['id']) ?></strong>
            </div>
            <div class="d-flex justify-between" style="font-size: 1.5rem; font-weight: bold; color: #e53935; border-top: 1px solid #ddd; padding-top: 15px; margin-top: 10px;">
                <span>Total Tagihan</span>
                <span>Rp <?= number_format($pesanan['total_tagihan'], 0, ',', '.') ?></span>
            </div>
        </div>

        <form action="<?= Helper::url('payment/' . $pesanan['id'] . '/process') ?>" method="POST" id="paymentForm">

            <input type="hidden" name="browser_fingerprint" id="browserFingerprint">

            <div class="form-group">
                <label class="form-label">Pilih Metode Pembayaran</label>
                <select name="payment_method" class="form-control" required style="font-size: 1.1rem; height: 50px;">
                    <option value="qris">QRIS (Otomatis Terscan)</option>
                    <option value="va_bca">BCA Virtual Account</option>
                    <option value="va_mandiri">Mandiri Virtual Account</option>
                </select>
            </div>

            <div class="form-group" style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee;">
                <label class="form-label text-danger">⚠️ Simulasi Saldo Rekening Anda (Mode Testing)</label>
                <p style="font-size: 0.85rem; color: #666; margin-top: -5px;">Ketikkan saldo yang lebih rendah dari total tagihan untuk melihat API menolak transaksi.</p>
                <input type="number" name="mock_balance" class="form-control" value="<?= $pesanan['total_tagihan'] + 50000 ?>" min="0" required>
            </div>

            <button type="submit" class="btn btn-success btn-block" style="height: 60px; font-size: 1.2rem; margin-top: 30px;">
                💳 Konfirmasi & Bayar Sekarang
            </button>
            <a href="<?= Helper::url('pesanan') ?>" class="btn btn-dark btn-block mt-10" style="text-align: center;">Batalkan Pembayaran</a>
        </form>
    </div>

</main>
