<?php

namespace App\Core;

/**
 * Kelas Session
 * Mengatur interaksi statis dengan $_SESSION untuk manajemen state.
 */
class Session
{
    /**
     * Menetapkan variabel sesi.
     *
     * @param string $key   Kunci sesi.
     * @param mixed  $value Nilai sesi.
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Mengambil variabel sesi.
     *
     * @param string $key     Kunci sesi.
     * @param mixed  $default Nilai balikan jika kunci tidak ada.
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Memeriksa apakah kunci sesi tertentu ada.
     *
     * @param string $key Kunci sesi.
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Menghapus kunci tertentu dari sesi.
     *
     * @param string $key Kunci sesi.
     * @return void
     */
    public static function remove(string $key): void
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Menghancurkan seluruh sesi pengguna.
     *
     * @return void
     */
    public static function destroy(): void
    {
        session_destroy();
    }

    /**
     * Menetapkan pesan Flash yang hanya hidup sementara.
     *
     * @param string $key     Kunci flash (success, error, dll).
     * @param string $message Isi pesan.
     * @return void
     */
    public static function setFlash(string $key, string $message = ''): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Memeriksa apakah Flash Message tertentu ada.
     *
     * @param string $key Kunci flash.
     * @return bool
     */
    public static function hasFlash(string $key = ''): bool
    {
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Mengambil Flash Message dan langsung menghapusnya.
     *
     * @param string $key Kunci flash.
     * @return string|null Pesan flash, atau null jika tidak ada.
     */
    public static function getFlash(string $key = ''): ?string
    {
        if (self::hasFlash($key)) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Alias untuk setFlash().
     *
     * @param string $type    Tipe notifikasi.
     * @param string $message Isi notifikasi.
     * @return void
     */
    public static function flash(string $type, string $message): void
    {
        self::setFlash($type, $message);
    }
}
