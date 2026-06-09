<?php

namespace App\Core;

use App\Services\ImageService;

/**
 * Kelas Abstrak Controller.
 * Menjadi fondasi bagi semua controller aplikasi untuk memuat view dan mengelola HTTP Response.
 */
abstract class Controller
{
    /** @var Request Objek pembungkus HTTP Request */
    protected Request $request;

    /** @var Session Objek pembungkus manajemen Session */
    protected Session $session;

    /**
     * Konstruktor Controller.
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->session = new Session();
    }

    /**
     * Memulai proses perangkaian (Fluent Building) untuk View.
     *
     * @param string $viewPath Path relatif dari folder View (contoh: 'user/katalog').
     * @return View Objek View builder yang siap dirangkai.
     */
    protected function view(string $viewPath): View
    {
        return new View($viewPath);
    }

    /**
     * Helper untuk memproses upload via Service dan menangani error-nya.
     *
     * @return string|null Path gambar jika berhasil, Null jika tidak ada file.
     * @throws \Exception Jika upload gagal/ditolak.
     */
    protected function handleImageUpload(?array $fileInput, string $folder, string $prefix): ?string
    {
        if (!$fileInput || $fileInput['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $path = \App\Services\ImageService::uploadAndCompress($fileInput, $folder, $prefix);

        if ($path === false) {
            $errorMsg = Session::getFlash('error') ?? 'Gagal memproses gambar.';
            throw new \Exception($errorMsg);
        }

        return $path;
    }
    /**
     * Mengalihkan (Redirect) pengguna ke URL tujuan secara paksa.
     *
     * @param string $url Alamat URL tujuan.
     * @return void
     */
    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }

    /**
     * Mengembalikan pengguna ke halaman sebelumnya (HTTP_REFERER).
     *
     * @param string $fallbackPath Path cadangan jika referer tidak ada.
     * @return void
     */
    protected function back(string $fallbackPath = 'katalog'): void
    {
        $referrer = $this->request->referrer(Helper::url($fallbackPath));
        Response::redirect($referrer);
    }

    /**
     * Menyimpan pesan flash sementara ke sesi.
     *
     * @param string $type    Tipe pesan (success, error, warning, info).
     * @param string $message Isi pesan.
     * @return void
     */
    protected function flash(string $type, string $message): void
    {
        Session::setFlash($type, $message); // Diubah memanggil static agar konsisten
    }

    /**
     * Menyimpan pesan flash dan langsung mengalihkan pengguna (PRG Pattern).
     *
     * @param string $type    Tipe pesan.
     * @param string $message Isi pesan.
     * @param string $url     URL tujuan.
     * @return void
     */
    protected function flashRedirect(string $type, string $message, string $url): void
    {
        $this->flash($type, $message);
        $this->redirect($url);
    }

    /**
     * Mengembalikan respon JSON ke browser dan menghentikan eksekusi script.
     *
     * @param mixed $data       Data array atau objek yang akan di-encode ke JSON.
     * @param int   $statusCode HTTP Status Code (default: 200).
     * @return void
     */
    protected function json(mixed $data, int $statusCode = Response::HTTP_OK): void
    {
        (new Response($data))->setStatusCode($statusCode)->send();
    }

    /**
     * Memulai perangkaian (Fluent) objek HTTP Response.
     *
     * @param string $content Teks / HTML mentah untuk dikirim.
     * @param int    $status  HTTP Status Code (default: 200).
     * @return Response
     */
    protected function response(string $content, int $status = Response::HTTP_OK): Response
    {
        return (new Response($content))->setStatusCode($status);
    }
}
