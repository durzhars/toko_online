<?php

namespace App\Services;

use App\Core\Session;

class ImageService
{
    public const ALLOWED_IMAGES = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Memproses upload file, memvalidasi ekstensi, dan memindahkannya ke direktori target.
     *
     * @param array $fileArray     Array file yang didapatkan dari klien.
     * @param string $targetFolder Folder tujuan relatif terhadap public/ (contoh: 'uploads/produk').
     * @param string $prefix       Awalan nama file unik (contoh: 'prod_').
     * @return string|null|bool Mengembalikan path relatif file jika sukses, null jika gagal, dan false jika ada error.
     */
    public static function uploadAndCompress(array $fileArray, string $targetFolder, string $prefix = 'file_'): string|null|bool
    {
        if (!isset($fileArray['error']) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fileArray['error'] !== UPLOAD_ERR_OK) {
            $maxSize = ini_get('upload_max_filesize');
            Session::flash('error', "Gagal mengunggah gambar. Ukuran file melebihi batas server ({$maxSize}).");
            return false;
        }

        $tmpPath = $fileArray['tmp_name'];
        $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, self::ALLOWED_IMAGES)) {
            Session::flash('error', "Ekstensi '{$ext}' tidak didukung! Gunakan: " . implode(', ', self::ALLOWED_IMAGES));
            return false;
        }

        $newName = uniqid($prefix) . '.' . $ext;
        $absoluteTargetDir = __DIR__ . '/../../public/' . trim($targetFolder, '/') . '/';

        if (!is_dir($absoluteTargetDir) && !mkdir($absoluteTargetDir, 0755, true)) {
            Session::flash('error', 'Gagal membuat folder penyimpanan di server.');
            return false;
        }

        $absoluteTargetFile = $absoluteTargetDir . $newName;

        if (self::compressImage($tmpPath, $absoluteTargetFile, 75)) {
            return '/' . trim($targetFolder, '/') . '/' . $newName;
        }

        Session::flash('error', "Gambar rusak atau gagal diproses oleh server.");
        return false;
    }

    /**
     * Fungsi Internal untuk memproses gambar menggunakan PHP GD Library.
     *
     * @param string $sourcePath Path file sementara (tmp_name).
     * @param string $targetPath Path tujuan untuk penyimpanan fisik.
     * @param int $quality Kualitas gambar (0-100).
     * @return bool Jika berhasil dikompres dan disimpan.
     **/
    private static function compressImage(string $sourcePath, string $targetPath, int $quality = 75): bool
    {
        $info = getimagesize($sourcePath);
        if ($info === false) {
            return false;
        }

        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                $result = imagejpeg($image, $targetPath, $quality);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                $pngQuality = round((100 - $quality) / 100 * 9);
                imagealphablending($image, false);
                imagesavealpha($image, true);
                $result = imagepng($image, $targetPath, $pngQuality);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                $result = imagewebp($image, $targetPath, $quality);
                break;
            default:
                return move_uploaded_file($sourcePath, $targetPath);
        }
        return $result;
    }
}
