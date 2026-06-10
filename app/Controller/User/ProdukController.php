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
    private Produk $produkModel;
    private Kategori $kategoriModel;
    private GaleriProduk $galeriModel;

    /**
     * 🚀 Inisialisasi Model di dalam Konstruktor
     */
    public function __construct()
    {
        parent::__construct(); // Wajib dipanggil jika Controller induk memiliki konstruktor

        $this->produkModel = new Produk();
        $this->kategoriModel = new Kategori();
        $this->galeriModel = new GaleriProduk();
    }

    /**
     * [GET] Menampilkan halaman utama katalog produk dengan fitur pencarian.
     *
     * @return void
     */
    public function katalog(): void
    {
        $keyword = $this->request->input('q');
        $kategoriId = $this->request->input('kategori');

        $daftarProduk = $this->produkModel->getKatalog($keyword, $kategoriId);
        $daftarKategori = $this->kategoriModel->findAll();

        $this->view('user/katalog')
            ->title('Katalog Produk')
            ->with([
                'daftarProduk'   => $daftarProduk,
                'daftarKategori' => $daftarKategori,
                'keyword'        => $keyword,
                'kategoriId'     => $kategoriId
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

        $produk = $this->produkModel->getOneProduk($id);

        if (!$produk) {
            $this->flashRedirect('error', 'Produk tidak ditemukan.', 'katalog');
        }

        $galeri = $this->galeriModel->getByProduk($id);
        $rekomendasi = $this->produkModel->getRekomendasi($produk['kategori_id'], $id, 4);

        $this->view('user/detail_produk')
            ->title($produk['nama_produk'])
            ->with([
                'produk' => $produk,
                'galeri' => $galeri,
                'rekomendasi' => $rekomendasi,
            ])
            ->render();
    }

    /**
     * [POST] Menghapus banyak produk sekaligus dari Database.
     */
    public function batchHapus(): void
    {
        if ($this->request->isMethod('POST')) {
            $hapusIds = $this->request->input('hapus_ids', []);

            if (!empty($hapusIds) && is_array($hapusIds)) {
                $berhasil = $this->produkModel->delete($hapusIds);
                if ($berhasil) {
                    $jumlah = count($hapusIds);
                    $this->flashRedirect('success', "$jumlah Produk berhasil dihapus permanen.", 'admin/produk');
                } else {
                    $this->flashRedirect('error', 'Gagal menghapus produk. Terjadi kesalahan pada database.', 'admin/produk');
                }
            } else {
                $this->flashRedirect('warning', 'Tidak ada produk yang dipilih untuk dihapus.', 'admin/produk');
            }
        }
    }
}
