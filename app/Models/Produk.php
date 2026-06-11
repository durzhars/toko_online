<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model Produk
 * Menangani entitas dan interaksi tabel `produk`.
 */
class Produk extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'produk';

    /** @var bool Mengaktifkan timestamps otomatis (created_at, updated_at) */
    protected bool $timestamps = true;

    /** @var array<string> Daftar kolom yang bisa diisi massal */
    protected array $fillable = [
        'kategori_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'path_gambar'
    ];

    /**
     * Mengambil daftar produk berdasarkan ID Kategori.
     *
     * @param int $kategoriId ID Kategori.
     * @return array<int, array<string, mixed>> Daftar produk.
     */
    public function getByKategori(int $kategoriId): array
    {
        $results = $this->query()
            ->where('kategori_id', '=', $kategoriId)
            ->where('is_deleted', '=', 0)
            ->get();

        return array_map(fn ($row) => $this->filterHidden($row), $results);
    }

    /**
     * Mengambil seluruh produk beserta nama kategorinya (JOIN).
     *
     * @return array<int, array<string, mixed>> Daftar produk lengkap.
     */
    public function getProdukLengkap(): array
    {
        $query = $this->query()
            ->select('produk.*', 'kategori.nama_kategori')
            ->join('kategori', 'produk.kategori_id = kategori.id')
            ->where('produk.is_deleted', '=', 0)
            ->orderBy('produk.created_at', 'DESC')
            ->get();

        return array_map(fn ($row) => $this->filterHidden($row), $query);
    }

    /**
     * Mengambil katalog produk dengan dukungan filter dan pencarian menggunakan Query Builder.
     *
     * @param string|null $keyword    Kata kunci pencarian nama atau deskripsi.
     * @param string|null $kategoriId Filter ID kategori.
     * @return array<int, array<string, mixed>> Hasil produk yang telah difilter.
     */
    public function getKatalog(?string $keyword = null, ?string $kategoriId = null): array
    {
        $query = $this->query()
            ->select('produk.*', 'kategori.nama_kategori')
            ->join('kategori', 'produk.kategori_id = kategori.id')
            ->where('produk.is_deleted', '=', 0);

        if (!empty($kategoriId)) {
            $query->where('produk.kategori_id', '=', $kategoriId);
        }

        if (!empty($keyword)) {
            $query->whereSearch(['produk.nama_produk', 'produk.deskripsi'], $keyword);
        }

        $results = $query->orderBy('produk.created_at', 'DESC')->get();

        return array_map(fn ($row) => $this->filterHidden($row), $results);
    }

    /**
     * Mengambil satu produk beserta nama kategorinya.
     *
     * @param string|int $id ID Produk.
     * @return array
     **/
    public function getOneProduk(string|int $id): array
    {
        $produk = $this->query()
            ->select('produk.*', 'kategori.nama_kategori')
            ->join('kategori', 'produk.kategori_id = kategori.id')
            ->where('produk.id', '=', $id)
            ->first();

        return $this->processOutput($produk);
    }

    /**
     * Mengambil produk rekomendasi berdasarkan kategori. Akan mengrcualikan produk yang sedang dilihat.
     *
     * @param int|string $kategoriId Kategori untuk produk saat ini
     * @param int|string $excludeId ID Produk yang sedang dipakai.
     * @param int $limit Batas maksimal rekomendasi.
     * @return array
     **/
    public function getRekomendasi(int|string $kategoriId, int|string $excludeId, int $limit = 4): array
    {
        $results = $this->query()
            ->select('produk.*', 'kategori.nama_kategori')
            ->join('kategori', 'produk.kategori_id = kategori.id')
            ->where('produk.kategori_id', '=', $kategoriId)
            ->where('produk.id', '!=', $excludeId)
            ->where('produk.is_deleted', '=', 0)
            ->orderBy('produk.created_at', 'DESC')
            ->limit($limit)
            ->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }

    /**
     * Menghapus data dari tabel berdasarkan Primary Key atau sekumpulan Primary Key.
     *
     * Method Override dari parent class Model.
     * Melakukan soft-delete dengan mengubah kolom is_deleted ke 1(true).
     * Mencegah rusaknya relasi data riwayat transaksi.
     *
     * @param int|string|array $id Target ID baris, atau array dari ID untuk dihapus massal.
     * @return bool TRUE jika penghapusan sukses, FALSE jika gagal.
     */
    public function delete(int|string|array $id): bool
    {
        if (is_array($id)) {
            if (empty($id)) {
                return false;
            }

            return $this->query()
                ->whereIn($this->primaryKey, $id)
                ->update(['is_deleted' => 1]);
        }

        return $this->query()
            ->where($this->primaryKey, '=', $id)
            ->update(['is_deleted' => 1]);
    }
}
