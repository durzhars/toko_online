<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model GaleriProduk
 *
 * Menangani tabel anak yang berisi kumpulan foto untuk satu entitas Produk.
 */
class GaleriProduk extends Model
{
    protected string $table = 'galeri_produk';
    protected bool $timestamps = true;

    protected array $fillable = [
        'produk_id',
        'path_gambar',
        'is_utama',
    ];

    /**
     * Mengambil semua foto untuk satu produk tertentu.
     *
     * Digunakan sebelum menetapkan foto lain sebagai is_utama.
     *
     * @param int|string $produkId ID produk.
     * @return array
     **/
    public function getByProduk(int|string $produkId): array
    {
        return $this->query()
            ->where('produk_id', '=', $produkId)
            ->orderBy('is_utama', 'DESC')
            ->get();
    }

    /**
     * Set semua foto dalam satu produk menjadi bukan yang utama.
     *
     * @param int|string $produkId ID Produk.
     * @return void
     **/
    public function resetUtama(int|string $produkId): void
    {
        $this->query()
            ->where('produk_id', '=', $produkId)
            ->update(['is_utama' => 0]);
    }
}
