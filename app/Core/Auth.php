<?php

namespace App\Core;

/**
 * Kelas utilitas otentikasi (Auth)
 * Mengelola pengecekan sesi dan otorisasi pengguna.
 */
class Auth
{
    /**
     * Memeriksa apakah ada sesi pengguna yang aktif.
     *
     * @return bool True jika user sudah login, False jika belum.
     */
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    /**
     * Memeriksa apakah pengguna yang sudah login memiliki role admin.
     *
     * @return bool True jika admin, False jika bukan atau belum login.
     */
    public static function isAdmin(): bool
    {
        return self::check() && Session::get('user_role') === 'admin';
    }

    /**
     * Mengambil nilai spesifik dari sesi pengguna yang sedang login.
     *
     * @param string $key Kunci sesi tanpa prefix 'user_' (contoh: 'nama', 'id').
     * @return mixed Nilai dari sesi, atau null jika tidak ditemukan.
     */
    public static function user(string $key): mixed
    {
        return Session::get('user_' . $key) ?? null;
    }

    /**
     * Mendaftarkan data pengguna ke dalam Sesi.
     *
     * @param array<string, mixed> $user Data array pengguna.
     * @return void
     */
    public static function login(array $user): void
    {
        Session::set('user_id', $user['id']);
        Session::set('user_nama', $user['nama']);
        Session::set('user_role', $user['role']);
    }

    /**
     * Menghapus seluruh data sesi pengguna saat ini.
     *
     * @return void
     */
    public static function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_nama');
        Session::remove('user_role');
    }

    /**
     * Middleware: Menendang pengguna ke halaman login jika belum terautentikasi.
     *
     * @return void
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            Response::redirect('login');
        }
    }

    /**
     * Middleware: Menendang pengguna ke halaman katalog jika bukan administrator.
     * Membutuhkan login terlebih dahulu.
     *
     * @return void
     */
    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            Session::flash('error', 'Akses Ditolak!');
            Response::redirect('katalog'); // Pastikan ini dilempar ke katalog, bukan login jika user ternyata pelanggan.
        }
    }
}
