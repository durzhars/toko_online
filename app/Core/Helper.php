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
     * @param array<string> $ignoreFiles Daftar kata kunci yang kebal penghapusan.
     * @return void
     **/
    public static function deleteFile(string $path, array $ignoreFiles = ['default-']): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, '/assets/')) {
            return;
        }

        foreach ($ignoreFiles as $ignore) {
            if (str_contains($path, $ignore)) {
                return;
            }
        }

        $absolutePath = __DIR__ . '/../../public' . $path;

        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
    /**
     * Autoloader (PSR-4 Standard)
     * Otomatis memuat file kelas tanpa menggunakan Composer.
     */
    public static function registerAutoloader(): void
    {
        spl_autoload_register(function (string $class) {
            $prefix = 'App\\';
            // __DIR__ adalah app/Core, jadi mundur satu level ke app/
            $base_dir = __DIR__ . '/../';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require_once $file;
            }
        });
    }
}
