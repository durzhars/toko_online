<?php

namespace App\Services;

/**
 * Mock Payment Gateway API.
 * Menyimulasikan entitas eksternal pengelola transaksi keuangan (seperti Midtrans/Xendit).
 * Kelas ini sama sekali tidak mengetahui struktur database aplikasi utama.
 */
class PaymentGateway
{
    /** @var string Kunci rahasia simulasi antara Merchant (Toko) dan Payment Gateway */
    private const MERCHANT_SECRET = 'QSHOP_SECURE_KEY_2026';

    /**
     * Memproses payload transaksi dari klien.
     * @param array<string, mixed> $payload Data transaksi dari merchant.
     * @return array<string, mixed> Respons state dari Payment Gateway.
     */
    public function charge(array $payload): array
    {
        // 1. Verifikasi Otorisasi Merchant
        if (!isset($payload['merchant_key']) || $payload['merchant_key'] !== self::MERCHANT_SECRET) {
            return $this->buildResponse(401, 'unauthorized', 'Akses ditolak: Kunci Merchant tidak valid.');
        }

        // 2. Fraud Detection: Verifikasi Fingerprint Browser & IP
        $fingerprint = $payload['device_fingerprint'] ?? '';
        if (empty($fingerprint) || strlen($fingerprint) < 15) {
            return $this->buildResponse(406, 'deny', 'Fraud Terdeteksi: Fingerprint perangkat tidak valid atau disembunyikan.');
        }

        // 3. Verifikasi Logika Keuangan
        $amount = (float) ($payload['gross_amount'] ?? 0);
        $metode = $payload['payment_method'] ?? 'unknown';

        // Simulasi saldo akun bank pengguna yang ditarik dari pihak ketiga (Virtual Account/E-Wallet)
        $simulasiSaldoBank = (float) ($payload['mock_bank_balance'] ?? 0);

        if ($amount <= 0) {
            return $this->buildResponse(400, 'failed', 'Nominal transaksi tidak boleh kosong.');
        }

        if ($simulasiSaldoBank < $amount) {
            return $this->buildResponse(402, 'failed', "Saldo tidak mencukupi untuk metode pembayaran $metode.");
        }

        // 4. Simulasi Jeda Jaringan (Network Latency)
        usleep(500000); // Jeda 0.5 detik agar terasa seperti memanggil API sungguhan

        // 5. Transaksi Berhasil - Kembalikan State 'Settlement' (Lunas)
        return [
            'status_code' => 200,
            'transaction_status' => 'settlement', // State konvensi standar API pembayaran
            'transaction_id' => 'PG-' . uniqid() . '-' . time(),
            'order_id' => $payload['order_id'],
            'gross_amount' => $amount,
            'payment_type' => $metode,
            'message' => 'Transaksi berhasil diverifikasi dan dibayar.'
        ];
    }

    /**
     * Helper internal untuk membakukan format respons API.
     */
    private function buildResponse(int $code, string $status, string $message): array
    {
        return [
            'status_code' => $code,
            'transaction_status' => $status,
            'message' => $message
        ];
    }
}
