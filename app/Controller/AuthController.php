<?php

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Users;
use Exception;

/**
 * Controller Autentikasi.
 * Menangani proses Login, Registrasi, dan Logout pengguna.
 */
class AuthController extends Controller
{
    /**
     * [GET] Menampilkan form login.
     *
     * @return void
     */
    public function login(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('katalog');
            }
        }
        $this->view('resources/login')
            ->title('Login')
            ->render();
    }

    /**
     * [POST] Memproses data kredensial login pengguna.
     *
     * @return void
     */
    public function prosesLogin(): void
    {
        if ($this->request->isMethod('POST')) {
            $email = $this->request->input('email');
            $password = $this->request->input('password');

            $userModel = new Users();
            $user = $userModel->auth($email, $password);

            if ($user) {
                Auth::login($user);
                $this->flash('success', "Selamat datang, {$user['nama']}!");
                if ($user['role'] === 'admin') {
                    $this->redirect('admin/dashboard');
                } else {
                    $this->redirect('katalog');
                }
            } else {
                $this->flashRedirect('error', 'Email atau Password salah!', 'login');
            }
        }
    }

    /**
     * [GET] Menampilkan form pendaftaran akun baru.
     *
     * @return void
     */
    public function register(): void
    {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('katalog');
            }
        }
        $this->view('resources/register')
            ->title('Registrasi')
            ->render();
    }

    /**
     * [POST] Memproses pendaftaran pengguna baru.
     *
     * @return void
     */
    public function prosesRegister(): void
    {
        if ($this->request->isMethod('POST')) {
            $nama = $this->request->input('nama');
            $email = $this->request->input('email');
            $password = $this->request->input('password');

            $userModel = new Users();

            try {
                $userModel->registerUser($nama, $email, $password);
                $this->flashRedirect('success', 'Registrasi berhasil. Silakan login.', 'login');
            } catch (Exception $e) {
                $this->flash('error', "Registrasi gagal: " . $e->getMessage());
                $this->back();
            }
        }
    }

    /**
     * [GET] Menghapus sesi dan melogout pengguna.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        $this->flashRedirect('info', 'Anda telah berhasil keluar', 'katalog');
    }
}
