<?php

namespace App\Controller\User;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlamatPengiriman;
use App\Models\Users;
use Exception;

/**
 * Controller Profil.
 * Menangani tampilan dan pembaruan data informasi pribadi pengguna.
 */
class ProfilController extends Controller
{
    /** @var Users Instance dari model Users */
    private Users $userModel;

    /**
     * Konstruktor ProfilController.
     * Memastikan hanya pengguna yang sudah login yang bisa mengakses menu profil.
     */
    public function __construct()
    {
        parent::__construct();

        Auth::requireLogin();
        $this->userModel = new Users();
    }

    /**
     * [GET] Menampilkan halaman formulir profil.
     *
     * @return void
     */
    public function index(): void
    {
        $userId = Auth::user('id');
        // Ambil data terbaru langsung dari database menggunakan ID di sesi
        $profil = $this->userModel->findById(Auth::user('id'));
        $alamatModel = new AlamatPengiriman();
        $daftarAlamat = $alamatModel->getByUser($userId);

        if (!$profil) {
            $this->flashRedirect('error', 'Data profil tidak ditemukan.', 'katalog');
        }

        $this->view('user/profil')
            ->title('Profil Saya')
            ->with([
                'profil' => $profil,
                'daftarAlamat' => $daftarAlamat,
            ])
            ->render();
    }

    /**
     * [PUT] Memproses pembaruan data profil ke database.
     *
     * @return void
     */
    public function update(): void
    {
        if ($this->request->isMethod('PUT')) {
            $nama = $this->request->input('nama');
            $no_hp = $this->request->input('no_hp');
            $path_gambar = $this->request->input('path_gambar');

            try {
                $this->userModel->update(Auth::user('id'), [
                    'nama' => $nama,
                    'no_hp' => $no_hp,
                    'path_gambar' => $path_gambar,
                ]);
                $this->session->set('user_nama', $nama);
                $this->flashRedirect('success', 'Profil berhasil diperbarui!', 'profil');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal memperbarui profil: ' . $e->getMessage());
                $this->back('profil');
            }
        }
    }

    /**
     * [GET] Menampilkan form penambahan alamat baru.
     *
     * @return void
     */
    public function tambahAlamat(): void
    {
        $this->view('user/partials/alamat_tambah')
            ->title('Tambah Alamat Pengiriman')
            ->render();
    }

    /**
     * [POST] Menyimpan alamat pengiriman baru ke buku alamat.
     *
     * @return void
     */
    public function storeAlamat(): void
    {
        if ($this->request->isMethod('POST')) {
            $label = $this->request->input('label'); // misal: Rumah, Kantor
            $penerima = $this->request->input('penerima');
            $noHp = $this->request->input('no_hp');
            $alamatLengkap = $this->request->input('alamat_lengkap');
            $userId = Auth::user('id');
            $isUtama = $this->request->input('is_utama') ? 1 : 0;

            $alamatModel = new AlamatPengiriman();

            try {
                // Cek apakah ini alamat pertama? Jika ya, jadikan utama
                if ($isUtama === 1) {
                    $alamatModel->resetUtama(Auth::user('id'));
                }

                $alamatModel->create([
                    'user_id' => $userId,
                    'label' => $label,
                    'penerima' => $penerima,
                    'no_hp' => $noHp,
                    'alamat_lengkap' => $alamatLengkap,
                    'is_utama' => $isUtama
                ]);

                $this->flashRedirect('success', "Alamat '$label' berhasil ditambahkan.", 'profil');
            } catch (Exception $e) {
                $this->flash('error', 'Gagal menambah alamat: ' . $e->getMessage());
                $this->back('profil');
            }
        }
    }

    /**
     * [PUT] Menjadikan alamat yang sudah ada sebagai alamat utama.
     *
     * @param string|int $id ID alamat
     */
    public function setUtama(string|int $id): void
    {
        if ($this->request->isMethod('PUT')) {
            $alamatModel = new AlamatPengiriman();
            $alamat = $alamatModel->findById($id);

            if (!$alamat || $alamat['user_id'] != Auth::user('id')) {
                $this->flashRedirect('error', 'Akses ditolak.', 'profil');
            }

            try {
                $alamatModel->resetUtama(Auth::user('id'));
                $alamatModel->update($id, ['is_utama' => 1]);
                $this->flashRedirect('success', 'Alamat utama berhasil diubah.', 'profil');
            } catch (Exception $e) {
                $this->flashRedirect('error', 'Terjadi kesalahan sistem.' . $e->getMessage(), 'profil');
            }
        }
    }

    /**
     * [DELETE] Menghapus alamat dari buku alamat.
     *
     * @param string|int $id ID Alamat
     * @return void
     */
    public function destroyAlamat(string|int $id): void
    {
        if ($this->request->isMethod('DELETE')) {
            $alamatModel = new AlamatPengiriman();
            $alamat = $alamatModel->findById($id);

            // Validasi keamanan: Pastikan alamat ada dan milik user yang sedang login
            if (!$alamat || $alamat['user_id'] != Auth::user('id')) {
                $this->flashRedirect('error', 'Akses ditolak atau alamat tidak ditemukan.', 'profil');
            }

            try {
                $alamatModel->delete($id);
                $this->flashRedirect('success', 'Alamat berhasil dihapus.', 'profil');
            } catch (Exception $e) {
                $this->flashRedirect('error', 'Gagal menghapus alamat.' . $e->getMessage(), 'profil');
            }
        }
    }
}
