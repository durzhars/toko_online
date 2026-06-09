<?php

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Helper;
use App\Models\Kategori;
use Exception;

/**
 * Controller Kategori Admin.
 * Menangani operasi CRUD untuk data Kategori Produk.
 */
class KategoriController extends Controller
{
    /** @var Kategori Instance dari model Kategori */
    private Kategori $kategoriModel;

    /**
     * Konstruktor KategoriController.
     * Memastikan hanya Admin yang memiliki akses.
     */
    public function __construct()
    {
        parent::__construct();

        Auth::requireAdmin();
        $this->kategoriModel = new Kategori();
    }

    /**
     * [GET] Menampilkan daftar seluruh kategori.
     *
     * @return void
     */
    public function index(): void
    {
        $kategori = $this->kategoriModel->findAll();

        $this->view('admin/kategori') // Ubah path menjadi index
            ->layout('admin')
            ->title('Kelola Kategori')
            ->with('kategori', $kategori)
            ->render();
    }

    /**
     * [GET] Menampilkan halaman form tambah kategori.
     *
     * @return void
     */
    public function create(): void
    {
        $this->view('admin/partials/tambah_kategori')
            ->layout('admin')
            ->title('Tambah Kategori Baru')
            ->render();
    }

    /**
     * [POST] Menyimpan data kategori baru ke database.
     *
     * @return void
     */
    public function store(): void
    {
        if ($this->request->isMethod('POST')) {
            $data = [
                'nama_kategori' => $this->request->input('nama_kategori'),
                'path_gambar' => '/assets/brand-logo.jpg'
            ];
            try {
                $fileInput = $this->request->file('path_gambar');
                $uploadedPath = $this->handleImageUpload($fileInput, 'uploads/kategori', 'cat_');
                if ($uploadedPath) {
                    $data['path_gambar'] = $uploadedPath;
                }

                $this->kategoriModel->create($data);
                $this->flashRedirect('success', 'Kategori berhasil ditambahkan', 'admin/kategori');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal menambah kategori: ' . $e->getMessage());
                $this->back();
            }
        }
    }

    /**
     * [GET] Menampilkan halaman form edit kategori berdasarkan ID.
     *
     * @param string|int $id ID Kategori.
     * @return void
     */
    public function edit(string|int $id): void
    {
        $kategori = $this->kategoriModel->findById($id);

        if (!$kategori) {
            $this->flashRedirect('error', 'Kategori tidak ditemukan.', 'admin/kategori');
        }

        $this->view('admin/partials/edit_kategori')
            ->layout('admin')
            ->title('Edit Kategori')
            ->with('kategori', $kategori)
            ->render();
    }

    /**
     * [PUT] Memperbarui data kategori berdasarkan ID.
     *
     * @param string|int $id ID Kategori.
     * @return void
     */
    public function update(string|int $id): void
    {
        if ($this->request->isMethod('PUT')) {
            $kategoriLama = $this->kategoriModel->findById($id);
            $data = ['nama_kategori' => $this->request->input('nama_kategori')];

            try {
                $fileInput = $this->request->file('path_gambar');
                $uploadedPath = $this->handleImageUpload($fileInput, 'uploads/kategori', 'cat_');
                if ($uploadedPath) {
                    $data['path_gambar'] = $uploadedPath;
                    Helper::deleteFile($kategoriLama['path_gambar'] ?? '');
                }

                $this->kategoriModel->update($id, $data);
                $this->flashRedirect('success', 'Kategori berhasil diperbarui', 'admin/kategori');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
                $this->back();
            }
        }
    }

    /**
     * [DELETE] Menghapus data kategori secara permanen.
     *
     * @param string|int $id ID Kategori.
     * @return void
     */
    public function destroy(string|int $id): void
    {
        if ($this->request->isMethod('DELETE')) {
            $kategori = $this->kategoriModel->findById($id);
            try {
                $this->kategoriModel->delete($id);
                Helper::deleteFile($kategori['path_gambar'] ?? '');
                $this->flashRedirect('success', 'Kategori telah dihapus', 'admin/kategori');
            } catch (Exception $e) {
                $this->flashRedirect(
                    'error',
                    'Gagal menghapus kategori. Mungkin sedang digunakan oleh produk lain. ' . $e->getMessage(),
                    'admin/kategori'
                );
            }
        }
    }
}
