<?php

namespace App\Controller\User;

use App\Core\Controller;
use App\Models\GaleriProduk;
use App\Models\Produk;
use App\Models\Kategori;

/**
 * Controller Produk.
 * Menangani tampilan katalog untuk pelanggan beserta fitur pencariannya.
 */
class ProdukController extends Controller
{
    /**
     * [GET] Menampilkan halaman utama katalog produk dengan fitur pencarian.
     *
     * @return void
     */
    public function katalog(): void
    {
        $keyword = $this->request->input('q');
        $kategoriId = $this->request->input('kategori');

        $produkModel = new Produk();
        $kategoriModel = new Kategori(); // Asumsi: Model Kategori sudah ada dan memiliki method findAll()

        // Ambil data produk berdasarkan filter
        $daftarProduk = $produkModel->getKatalog($keyword, $kategoriId);

        // Ambil daftar kategori untuk dropdown filter
        $daftarKategori = $kategoriModel->findAll();

        $this->view('user/katalog')
            ->title('Katalog Produk')
            ->with([
                'daftarProduk'   => $daftarProduk,
                'daftarKategori' => $daftarKategori,
                'keyword'        => $keyword,       // Untuk mempertahankan isi input text
                'kategoriId'     => $kategoriId     // Untuk mempertahankan pilihan dropdown
            ])
            ->render();
    }

    /**
     * [GET] Menampilkan halaman rincian satu produk.
     *
     * @param string|int $id ID Produk
     * @return void
     **/
    public function show(string|int $id): void
    {
        $produkModel = new Produk();
        $galeriModel = new GaleriProduk();

        $produk = $produkModel->getOneProduk($id);

        if (!$produk) {
            $this->flashRedirect('error', 'Produk tidak ditemukan.', 'katalog');
        }

        $galeri = $galeriModel->getByProduk($id);
        $rekomendasi = $produkModel->getRekomendasi($produk['kategori_id'], $id, 4);

        $this->view('user/detail_produk')
            ->title($produk['nama_produk'])
            ->with([
                'produk' => $produk,
                'galeri' => $galeri,
                'rekomendasi' => $rekomendasi,
            ])
            ->render();
    }
}
