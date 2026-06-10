<?php

namespace App\Controller\User;

use App\Core\Controller;
use App\Models\Produk;

/**
 * Controller Keranjang.
 * Menangani operasi penambahan, penampilan, dan penghapusan item di keranjang belanja (Session).
 */
class KeranjangController extends Controller
{
    /**
     * [GET] Menampilkan halaman keranjang belanja beserta kalkulasi subtotal.
     *
     * @return void
     */
    public function index(): void
    {
        $keranjang = $this->session->get('keranjang', []);
        $produkModel = new Produk();
        $detailKeranjang = [];
        $totalTagihan = 0;

        foreach ($keranjang as $id => $jumlah) {
            $produk = $produkModel->findById($id);
            if ($produk) {
                $subtotal = $produk['harga'] * $jumlah;
                $totalTagihan += $subtotal;
                $produk['jumlah'] = $jumlah;
                $produk['subtotal'] = $subtotal;
                $detailKeranjang[] = $produk;
            }
        }

        $this->view('user/keranjang')
            ->title('Keranjang Belanja')
            ->with([
                'item' => $detailKeranjang,
                'total' => $totalTagihan,
            ])
            ->render();
    }

    /**
     * [POST] Menambahkan produk ke dalam keranjang belanja.
     * Mendukung Request standar maupun AJAX (Fetch API).
     *
     * @return void
     */
    public function tambah(): void
    {
        if ($this->request->isMethod('POST')) {
            $produkId = $this->request->input('produk_id');
            $jumlahBaru = (int) $this->request->input('jumlah', 1);
            if ($produkId && $jumlahBaru > 0) {
                $keranjang = $this->session->get('keranjang', []);
                if (isset($keranjang[$produkId])) {
                    $keranjang[$produkId] += $jumlahBaru;
                } else {
                    $keranjang[$produkId] = $jumlahBaru;
                }
                $this->session->set('keranjang', $keranjang);
            }

            $keranjang = $this->session->get('keranjang', []);
            $totalItems = array_sum($keranjang);

            // Respon khusus jika request datang dari JavaScript (Fetch API)
            if ($this->request->isAjax()) {
                $this->json([
                    'status' => 'success',
                    'pesan' => 'Berhasil Ditambahkan',
                    'total_items' => $totalItems,
                ]);
                return;
            }

            $this->flash('success', 'Produk berhasil ditambahkan ke keranjang');
            $this->back();
        }
    }

    /**
     * [GET/DELETE] Menghapus item dari keranjang menggunakan parameter URL dinamis.
     *
     * @param string|int $id ID Produk yang akan dihapus.
     * @return void
     */
    public function hapus(string|int $id): void
    {
        $keranjang = $this->session->get('keranjang', []);

        if (isset($keranjang[$id])) {
            unset($keranjang[$id]);
            $this->session->set('keranjang', $keranjang);
            $this->flashRedirect('info', 'Item telah dihapus dari keranjang.', 'keranjang');
        } else {
            $this->redirect('keranjang');
        }
    }
}
