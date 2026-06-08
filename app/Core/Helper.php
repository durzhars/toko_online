<?php

namespace App\Core;

/**
 * Kelas Helper Global.
 * Menyediakan fungsi-fungsi utilitas statis untuk mempermudah operasi sistem.
 */
class Helper
{
    /**
     * Membaca dan mengambil nilai dari variabel environment (.env).
     * Menggunakan caching statis agar file .env tidak di-parsing berulang kali.
     *
     * @param string $key Nama variabel env yang ingin diambil.
     * @param mixed $default Nilai kembalian default jika kunci tidak ditemukan.
     * @return mixed Nilai variabel environment, atau nilai default.
     */
    public static function env(string $key, mixed $default = null): mixed
    {
        static $envData = null;

        if ($envData === null) {
            $envPath = __DIR__ . '/../../.env';
            if (file_exists($envPath)) {
                $envData = parse_ini_file($envPath);
            } else {
                $envData = [];
            }
        }

        return $envData[$key] ?? $default;
    }

    /**
     * Membangun URL berdasarkan APP_URL di .env
     *
     * @param string $path Path relatif
     * @return string URL penuh
     **/
    public static function url(string $path = ''): string
    {
        $baseUrl = self::env('APP_URL', 'http://localhost');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Menghapus file fisik dari directory server
     *
     * @param string $path Path relatif file (contoh: '/uploads/produk/file.jpg').
     * @param string $ignoreFile Nama file yang kebal penghapusan (contoh: default-product.jpg).
     * @return void
     **/
    public static function deleteFile(string $path, string $ignoreFile = 'default-'): void
    {
        if (!empty($path) && !str_contains($path, $ignoreFile)) {
            $absolutePath = __DIR__ . '/../../public' . $path;
            if (file_exists($absolutePath) && is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }
    }
}
