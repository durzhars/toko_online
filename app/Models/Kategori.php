<?php

namespace App\Models;

use App\Core\Model;

class Kategori extends Model
{
    protected string $table = 'kategori';
    protected bool $timestamps = true;

    protected array $fillable = [
        'nama_kategori',
        'path_gambar',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->table = 'kategori';
    }

    /**
     * Mengambil semua kategori yang aktif (belum dihapus).
     * Override dari Base Model.
     */
    public function findAll(): array
    {
        $results = $this->query()
            ->where('is_deleted', '=', 0)
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
