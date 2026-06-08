<?php

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Exception;

class TransaksiController extends Controller
{
    private Transaksi $transaksiModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
        $this->transaksiModel = new Transaksi();
    }

    /**
     * [GET] Menampilkan seluruh pesanan masuk.
     */
    public function index(): void
    {
        $pesanan = $this->transaksiModel->getAllLengkap();

        $this->view('admin/transaksi')
            ->layout('admin')
            ->title('Pesanan Masuk')
            ->with('pesanan', $pesanan)
            ->render();
    }

    /**
     * [GET] Menampilkan detail pesanan beserta barang yang harus di-packing.
     *
     * @param string $id ID pesanan.
     */
    public function show(string $id): void
    {
        $pesanan = $this->transaksiModel->findById($id);
        if (!$pesanan) {
            $this->flashRedirect('error', 'Data pesanan tidak ditemukan.', 'admin/pesanan');
        }

        $detailModel = new DetailTransaksi();
        $items = $detailModel->getDetailAll($id);
        $this->view('admin/partials/detail_transaksi')
            ->layout('admin')
            ->title('Detail Pesanan: ' . $id)
            ->with([
                'pesanan' => $pesanan,
                'items' => $items
            ])
            ->render();
    }

    /**
     * [PUT] Memperbarui status pesanan dan input nomor resi.
     *
     * @param string $id ID Pesanan.
     */
    public function updateStatus(string $id): void
    {
        if ($this->request->isMethod('PUT')) {
            $status = $this->request->input('status');
            $resi = $this->request->input('resi_pengiriman');

            try {
                $this->transaksiModel->update($id, [
                    'status' => $status,
                    'resi_pengiriman' => empty($resi) ? null : $resi
                ]);
                $this->flashRedirect('success', 'Status pesanan berhasil diperbarui!', 'admin/pesanan/' . $id);
            } catch (Exception $e) {
                $this->flash('error', 'Gagal memperbarui status: ' . $e->getMessage());
                $this->back();
            }
        }
    }
}
