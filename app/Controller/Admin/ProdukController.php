<?php

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Helper;
use App\Models\Kategori;
use App\Models\Produk;
use Exception;

class ProdukController extends Controller
{
    private Produk $produkModel;
    private Kategori $kategoriModel;

    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();

        $this->produkModel = new Produk();
        $this->kategoriModel = new Kategori();
    }

    public function index(): void
    {
        $produk = $this->produkModel->getProdukLengkap();

        $this->view('admin/produk')
            ->layout('admin')
            ->title('Manajemen Produk')
            ->with('produk', $produk)
            ->render();
    }

    public function create(): void
    {
        $kategori = $this->kategoriModel->findAll();

        $this->view('admin/partials/tambah_produk')
            ->layout('admin')
            ->title('Tambah Produk Baru')
            ->with('kategori', $kategori)
            ->render();
    }

    public function edit(string|int $id): void
    {
        $produk = $this->produkModel->findById($id);

        if (!$produk) {
            $this->flashRedirect('error', 'Produk tidak ditemukan.', 'admin/produk');
        }

        $this->view('admin/partials/edit_produk')
            ->layout('admin')
            ->title('Edit Informasi Produk')
            ->with([
                'produk' => $produk,
                'kategori' => $this->kategoriModel->findAll(),
            ])
            ->render();
    }

    public function store(): void
    {
        if ($this->request->isMethod('POST')) {
            $data = [
                'nama_produk' => $this->request->input('nama_produk'),
                'kategori_id' => $this->request->input('kategori_id'),
                'harga' => $this->request->input('harga'),
                'stok' => $this->request->input('stok'),
                'deskripsi' => $this->request->input('deskripsi'),
                'path_gambar' => '/img/default-product.jpg',
            ];

            $uploadedPath = $this->request->uploadFile('path_gambar', 'uploads/produk', 'prod_');
            if ($uploadedPath) {
                $data['path_gambar'] = $uploadedPath;
            }

            try {
                $this->produkModel->create($data);
                $this->flashRedirect('success', 'Produk berhasil ditambahkan.', 'admin/produk');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal menambah produk: ' . $e->getMessage());
                $this->back();
            }
        }
    }


    public function update(string|int $id): void
    {
        if ($this->request->isMethod('PUT')) {
            $produkLama = $this->produkModel->findById($id);

            $data = [
                'nama_produk' => $this->request->input('nama_produk'),
                'kategori_id' => $this->request->input('kategori_id'),
                'harga' => $this->request->input('harga'),
                'stok' => $this->request->input('stok'),
                'deskripsi' => $this->request->input('deskripsi'),
            ];

            $uploadedPath = $this->request->uploadFile('path_gambar', 'uploads/produk', 'prod_');
            if ($uploadedPath) {
                $data['path_gambar'] = $uploadedPath;
                Helper::deleteFile($produkLama['path_gambar'] ?? '');
            }

            try {
                $this->produkModel->update($id, $data);
                $this->flashRedirect('success', 'Data produk berhasil diubah.', 'admin/produk');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal memperbarui produk: ' . $e->getMessage());
                $this->back();
            }
        }
    }

    public function destroy(string|int $id): void
    {
        if ($this->request->isMethod('DELETE')) {
            $produk = $this->produkModel->findById($id);

            try {
                $this->produkModel->delete($id);
                Helper::deleteFile($produk['path_gambar'] ?? ''); // 🚀 Bersihkan file saat dihapus

                $this->flashRedirect('success', 'Produk telah dihapus.', 'admin/produk');
            } catch (Exception $e) {
                $this->flashRedirect(
                    'error',
                    'Gagal menghapus produk. Mungkin masih terkait dengan transaksi: ' . $e->getMessage(),
                    'admin/produk'
                );
            }
        }
    }
}
