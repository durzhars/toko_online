<?php

namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * Model Users
 * Menangani manajemen akun pelanggan dan admin.
 */
class Users extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'users';

    /** @var bool Mengaktifkan timestamps otomatis */
    protected bool $timestamps = true;

    /** @var array<string> Daftar kolom yang bisa diisi massal */
    protected array $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'role'
    ];

    /** @var array<string> Kolom yang disembunyikan saat pemanggilan data */
    protected array $hidden = [
        'password'
    ];

    /**
     * Mencari pengguna berdasarkan email secara unik.
     *
     * @param string $email Email pengguna.
     * @return array<string, mixed>|bool Data array pengguna, atau false jika tidak ditemukan.
     */
    public function findByEmail(string $email): array|bool
    {
        return $this->query()
            ->where('email', '=', $email)
            ->first();
    }

    /**
     * Memverifikasi otentikasi login pengguna.
     *
     * @param string $email    Email pengguna.
     * @param string $password Password mentah dari input.
     * @return array<string, mixed>|false Array data tersensor jika cocok, atau false jika gagal.
     */
    public function auth(string $email, string $password): array|false
    {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $this->filterHidden($user);
        }
        return false;
    }

    /**
     * Memproses pendaftaran pengguna baru.
     *
     * @param string $name     Nama lengkap pengguna.
     * @param string $email    Alamat email unik.
     * @param string $password Password yang akan di-hash.
     * @return bool True jika berhasil.
     * @throws Exception Jika email sudah terdaftar.
     */
    public function registerUser(string $name, string $email, string $password): bool
    {
        if ($this->findByEmail($email)) {
            throw new Exception('Email Sudah Terdaftar!');
        }

        return $this->create([
            'nama' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'pelanggan'
        ]);
    }
}
