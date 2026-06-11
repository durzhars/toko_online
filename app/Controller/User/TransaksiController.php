<?php

namespace App\Controller\User;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlamatPengiriman;
use App\Models\Produk;
use App\Models\Transaksi;
use Exception;

/**
 * Controller Transaksi.
 * Menangani proses checkout keranjang dan tampilan riwayat pesanan.
 */
class TransaksiController extends Controller
{
    /**
     * [GET] Menampilkan halaman ringkasan checkout.
     *
     * @return void
     */
    public function checkout(): void
    {
        Auth::requireLogin();

        $keranjang = $this->session->get('keranjang', []);
        if (empty($keranjang)) {
            $this->redirect('katalog');
        }

        $produkModel = new Produk();
        $alamatModel = new AlamatPengiriman();

        $totalTagihan = 0;
        $detailPesanan = [];

        foreach ($keranjang as $id => $jumlah) {
            $produk = $produkModel->findById($id);
            if ($produk) {
                $subtotal = $produk['harga'] * $jumlah;
                $totalTagihan += $subtotal;

                $produk['jumlah'] = $jumlah;
                $produk['subtotal'] = $subtotal;
                $detailPesanan[] = $produk;
            }
        }

        $daftarAlamat = $alamatModel->getByUser(Auth::user('id'));

        $this->view('user/checkout')
            ->title('Checkout Pesanan')
            ->with([
                'item' => $detailPesanan,
                'total' => $totalTagihan,
                'daftarAlamat' => $daftarAlamat,
            ])
            ->render();
    }

    /**
     * [POST] Memproses pengiriman data checkout ke database.
     *
     * @return void
     */
    public function prosesCheckout(): void
    {
        Auth::requireLogin();

        if ($this->request->isMethod('POST')) {
            $alamatId = $this->request->input('alamat_id');
            $keranjang = $this->session->get('keranjang', []);

            if (empty($keranjang)) {
                $this->flashRedirect('warning', 'Keranjang Anda kosong!', 'katalog');
            }

            if ($alamatId === 'baru') {
                $penerima = $this->request->input('penerima_baru');
                $noHp = $this->request->input('no_hp_baru');
                $alamatLengkap = $this->request->input('alamat_lengkap_baru');

                if (empty($penerima) || empty($noHp) || empty($alamatLengkap)) {
                    $this->flashRedirect('error', 'Harap isi semua kolom alamat baru!', 'checkout');
                }
                $alamatFinal = [
                    'label' => 'Alamat Sekali Pakai',
                    'penerima' => $penerima,
                    'no_hp' => $noHp,
                    'alamat_lengkap' => $alamatLengkap,
                ];
            } else {
                $alamatModel = new AlamatPengiriman();
                $alamatTerpilih = $alamatModel->findById($alamatId);

                if (!$alamatTerpilih || $alamatTerpilih['user_id'] != Auth::user('id')) {
                    $this->flashRedirect('error', 'Alamat tidak valid atau bukan milik anda.', 'checkout');
                }
                $alamatFinal = [
                    'label' => $alamatTerpilih['label'],
                    'penerima' => $alamatTerpilih['penerima'],
                    'no_hp' => $alamatTerpilih['no_hp'],
                    'alamat_lengkap' => $alamatTerpilih['alamat_lengkap'],
                ];
            }

            $transaksiModel = new Transaksi();

            try {
                // Eksekusi transaksi database (memotong stok dan mencatat pesanan)
                $transaksiId = $transaksiModel->prosesCheckout(Auth::user('id'), $alamatFinal, $keranjang);
                $this->session->remove('keranjang');

                $this->flashRedirect('success', "Pesanan berhasil dibuat! ID: '$transaksiId'", 'pesanan');
            } catch (Exception $e) {
                // Error tertangkap, beri pesan flash dan kembalikan pengguna
                $this->flashRedirect('error', "Gagal memproses pesanan: " . $e->getMessage(), 'keranjang');
            }
        }
    }

    /**
     * [GET] Menampilkan daftar riwayat pesanan milik pengguna yang sedang login.
     *
     * @return void
     */
    public function pesananSaya(): void
    {
        Auth::requireLogin();

        $transaksiModel = new Transaksi();
        $riwayatPesanan = $transaksiModel->getByUser(Auth::user('id'));

        $this->view('user/pesanan')
            ->title('Pesanan Saya')
            ->with('pesanan', $riwayatPesanan)
            ->render();
    }

    /**
     * [POST] Konfirmasi pesanan yang telah dikirim.
     *
     * @param string $id ID Transaksi.
     * @return void
     **/
    public function selesaikanPesanan(string $id): void
    {
        Auth::requireLogin();

        if ($this->request->isMethod('POST')) {
            $transaksiModel = new Transaksi();
            $pesanan = $transaksiModel->findById($id);

            if ($pesanan && $pesanan['user_id'] == Auth::user('id') && $pesanan['status'] === 'SHIPPED') {
                $berhasil = $transaksiModel->update($id, ['status' => 'COMPLETED']);

                if ($berhasil) {
                    $this->flashRedirect('success', 'Terima kasih! Pesanan telah ditandai selesai.', 'pesanan');
                } else {
                    $this->flashRedirect('error', 'Gagal memperbarui status pesanan.', 'pesanan');
                }
            } else {
                $this->flashRedirect('warning', 'Pesanan tidak valid atau belum bisa diselesaikan.', 'pesanan');
            }
        }
    }
}
