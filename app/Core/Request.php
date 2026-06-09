<?php

namespace App\Core;

/**
 * Kelas Request
 *
 * Membungkus Global Arrays PHP ($_GET, $_POST, $_SERVER, $_FILES)
 * dan JSON Payloads dengan aman.
 */
class Request
{
    /** @var array<string, mixed>|null Cache untuk body JSON agar tidak dibaca berulang kali */
    private ?array $jsonPayload = null;

    /** @var array<string> Ekstensi gambar standar yang diizinkan secara global */
    public const ALLOWED_IMAGES = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Mengambil nilai dari global $_SERVER secara aman.
     *
     * @param string $key     Kunci array server.
     * @param mixed  $default Nilai kembali default.
     * @return mixed
     */
    public function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[strtoupper($key)] ?? $default;
    }

    /**
     * Mendapatkan HTTP Method yang sedang digunakan (mendukung Spoofing).
     *
     * @return string Method HTTP (GET, POST, PUT, DELETE, dll).
     */
    public function method(): string
    {
        $method = $this->server('REQUEST_METHOD', 'GET');
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofed = strtoupper($_POST['_method']);
            if (in_array($spoofed, ['PUT', 'PATCH', 'DELETE'])) {
                return $spoofed;
            }
        }
        return strtoupper($method);
    }

    /**
     * Memeriksa apakah request saat ini menggunakan method tertentu.
     *
     * @param string $method Target method yang dicek.
     * @return bool True jika cocok.
     */
    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    /**
     * Membaca dan men-decode body JSON dari request.
     *
     * @return array<string, mixed>
     */
    public function getJsonPayload(): array
    {
        if ($this->jsonPayload === null) {
            $input = file_get_contents('php://input');
            $this->jsonPayload = json_decode($input, true) ?? [];
        }
        return $this->jsonPayload;
    }

    /**
     * Mengambil seluruh data input gabungan $_GET dan $_POST.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $data = array_merge($_GET, $_POST);
        if (str_contains($$this->server('CONTENT_TYPE', ''), 'application/json')) {
            $data = array_merge($data, $this->getJsonPayload());
        }
        return $data;
    }

    /**
     * Mengambil nilai input. Memprioritaskan $_POST/$_GET, lalu fallback ke JSON Payload.
     *
     * @param string $key     Nama input.
     * @param mixed  $default Nilai default.
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        if (str_contains($this->server('CONTENT_TYPE', ''), 'application/json')) {
            $json = $this->getJsonPayload();
            if (isset($json[$key])) {
                return $json[$key];
            }
        }
        return $default;
    }

    /**
     * Mengambil URL referer (halaman sebelumnya).
     *
     * @param string $fallback URL cadangan jika referer kosong.
     * @return string
     */
    public function referrer(string $fallback): string
    {
        return $this->server('HTTP_REFERER', $fallback);
    }

    /**
     * Mendeteksi apakah request dikirim melalui XMLHttpRequest (AJAX/Fetch).
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        $accept = $this->server('HTTP_ACCEPT', '');
        $requestedWith = $this->server('HTTP_X_REQUESTED_WITH', '');

        return str_contains($accept, 'application/json') || strtolower($requestedWith) === 'xmlhttprequest';
    }

    /**
     * Mendapatkan URI dari request saat ini.
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->server('REQUEST_URI', '/');
    }

    /**
     * Memeriksa apakah sebuah file diunggah tanpa error.
     *
     * @param string $key Nama input file.
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Memproses upload file, memvalidasi ekstensi, dan memindahkannya ke direktori target.
     *
     * @param string $inputName    Nama field input file dari form.
     * @param string $targetFolder Folder tujuan relatif terhadap public/ (contoh: 'uploads/produk').
     * @param string $prefix       Awalan nama file unik (contoh: 'prod_').
     * @param array<string> $allowedExt Array ekstensi yang diizinkan (default: ALLOWED_IMAGES).
     * @return string|null Mengembalikan path relatif file jika sukses, atau null jika gagal.
     */
    public function uploadFile(string $inputName, string $targetFolder, string $prefix = 'file_', array $allowedExt = self::ALLOWED_IMAGES): ?string
    {
        // Jika form tidak mengirim file sama sekali, return null (gunakan default)
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$inputName];

        // Cek apakah ada error dari sisi server PHP (misal: file kebesaran)
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $maxSize = ini_get('upload_max_filesize');
            throw new \Exception("Gagal mengunggah file. Kode Error: {$file['error']} (Maksimal ukuran file di server ini: {$maxSize})");
        }

        $tmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi Ekstensi File
        if (!in_array($ext, $allowedExt)) {
            throw new \Exception("Ekstensi file '{$ext}' tidak diizinkan! Gunakan: " . implode(', ', $allowedExt));
        }

        $newName = uniqid($prefix) . '.' . $ext;
        $absoluteTargetDir = __DIR__ . '/../../public/' . trim($targetFolder, '/') . '/';

        // Pastikan direktori tersedia
        if (!is_dir($absoluteTargetDir)) {
            if (!mkdir($absoluteTargetDir, 0755, true)) {
                throw new \Exception("Gagal membuat direktori penyimpanan gambar di server. Dir: $absoluteTargetDir Nama: $newName");
            }
        }

        $absoluteTargetFile = $absoluteTargetDir . $newName;

        // Pindahkan file dan cek kegagalan permission
        if (move_uploaded_file($tmpPath, $absoluteTargetFile)) {
            return '/' . trim($targetFolder, '/') . '/' . $newName;
        }

        throw new \Exception("Gagal memindahkan gambar. Silakan periksa 'Write Permission' pada folder public/uploads.");
    }

    /**
     * Mendeteksi apakah request saat ini menggunakan protokol HTTPS yang aman
     * (Mendukung pendeteksian di balik Reverse Proxy seperti Nginx atau Cloudflare).
     *
     * @return bool
     */
    public function isHttps(): bool
    {
        // Cek header standar dari web server
        $https = strtolower((string) $this->server('HTTPS', 'off'));
        if ($https === 'on' || $https === '1') {
            return true;
        }

        // Cek port server standar HTTPS
        if ($this->server('SERVER_PORT') == 443) {
            return true;
        }

        // Cek header Forwarded dari Reverse Proxy (Cloudflare / Nginx)
        $forwardedProto = strtolower((string) $this->server('HTTP_X_FORWARDED_PROTO', ''));
        if ($forwardedProto === 'https') {
            return true;
        }

        return false;
    }
}
