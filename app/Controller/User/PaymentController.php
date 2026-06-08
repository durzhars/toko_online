<?php

namespace App\Controller\User;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Transaksi;
use App\Services\PaymentGateway;
use Exception;

class PaymentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireLogin();
    }

    /**
     * [GET] Menampilkan UI Payment Gateway untuk transaksi tertentu.
     */
    public function index(string $id): void
    {
        $transaksiModel = new Transaksi();
        $pesanan = $transaksiModel->findById($id);

        // Validasi kepemilikan dan status
        if (!$pesanan || $pesanan['user_id'] !== Auth::user('id')) {
            $this->flashRedirect('error', 'Pesanan tidak ditemukan atau akses ditolak.', 'pesanan');
        }

        if ($pesanan['status'] !== 'PENDING') {
            $this->flashRedirect('warning', 'Pesanan ini sudah diproses atau dibayar.', 'pesanan');
        }

        $this->view('user/bayar')
            ->title('Selesaikan Pembayaran')
            ->with('pesanan', $pesanan)
            ->render();
    }

    /**
     * [POST] Memproses pembayaran dengan melempar request ke Layanan PaymentGateway.
     */
    public function process(string $id): void
    {
        if ($this->request->isMethod('POST')) {
            $transaksiModel = new Transaksi();
            $pesanan = $transaksiModel->findById($id);

            if (!$pesanan || $pesanan['status'] !== 'PENDING') {
                $this->flashRedirect('error', 'Transaksi tidak valid.', 'pesanan');
            }
            $paymentMethod = $this->request->input('payment_method');
            $mockBalance = $this->request->input('mock_balance', 0); // Simulasi saldo dari form UI
            $clientFingerprint = $this->request->input('browser_fingerprint', '');
            $apiPayload = [
                'merchant_key' => 'QSHOP_SECURE_KEY_2026', // Harus cocok dengan di Service
                'order_id' => $pesanan['id'],
                'gross_amount' => $pesanan['total_tagihan'],
                'payment_method' => $paymentMethod,
                'device_fingerprint' => $clientFingerprint,
                'mock_bank_balance' => $mockBalance,
                'customer_email' => Auth::user('email')
            ];
            $gateway = new PaymentGateway();
            $apiResponse = $gateway->charge($apiPayload);

            if ($apiResponse['status_code'] === 200 && $apiResponse['transaction_status'] === 'settlement') {
                try {
                    $transaksiModel->update($id, [
                        'status' => 'PAID',
                        'bukti_bayar' => $apiResponse['transaction_id'] // Simpan ID referensi Gateway
                    ]);
                    $this->flashRedirect('success', 'Pembayaran Berhasil! Pesanan Anda segera diproses.', 'pesanan');
                } catch (Exception $e) {
                    $this->flashRedirect('error', 'Kesalahan internal saat menyimpan status.' . $e->getMessage(), 'pesanan');
                }
            } else {
                $pesanError = $apiResponse['message'] ?? 'Pembayaran ditolak oleh sistem.';
                $this->flash('error', $pesanError);
                $this->back();
            }
        }
    }
}
