<?php

namespace App\Core;

/**
 * Kelas Abstrak Base Model.
 * Menyediakan fondasi metode CRUD standar dan akses database terpusat untuk semua kelas Model turunan.
 * Mengimplementasikan fitur perlindungan Mass Assignment, Penyensoran Atribut (Hidden), dan Timestamps otomatis.
 */
abstract class Model
{
    /** @var Database Instance pembungkus database PDO */
    protected Database $db;

    /** @var string Nama tabel database */
    protected string $table;

    /** @var string Nama Primary Key (default: 'id') */
    protected string $primaryKey = 'id';

    /** @var array<string> Daftar kolom Mass Assignment Protection */
    protected array $fillable = [];

    /** @var array<string> Daftar kolom sensitif (Hidden) */
    protected array $hidden = [];

    /**
     * Penentu tipe data otomatis (Casting).
     * Contoh: ['alamat_pengiriman' => 'json']
     *
     * @var array<string, string>
     */
    protected array $casts = [];

    /** @var bool Mengelola 'created_at' dan 'updated_at' otomatis */
    protected bool $timestamps = false;

    /**
     * Menginisialisasi koneksi database saat kelas model dipanggil.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Memulai instansi Query Builder khusus untuk model ini.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder($this->db, $this->table);
    }

    /**
     * Menyaring array data yang masuk, membuang kunci yang tidak terdaftar di properti $fillable.
     *
     * @param array<string, mixed> $data Data mentah yang akan disaring.
     * @return array<string, mixed> Data yang sudah dibersihkan dan aman untuk dimasukkan ke database.
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Menyuntikkan waktu saat ini ke dalam array data untuk kolom created_at dan updated_at.
     *
     * @param array<string, mixed> $data Data yang akan disuntikkan timestamp.
     * @param bool $isUpdate Flag penanda apakah ini operasi update (jika true, created_at diabaikan).
     * @return array<string, mixed> Data yang sudah memiliki atribut timestamp.
     */
    protected function setTimestamps(array $data, bool $isUpdate = false): array
    {
        if (!$this->timestamps) {
            return $data;
        }

        $now = date('Y-m-d H:i:s');

        if (!$isUpdate) {
            $data['created_at'] = $now;
        }

        $data['updated_at'] = $now;
        return $data;
    }

    /**
     * Menyembunyikan kolom sensitif dari array hasil query berdasarkan properti $hidden.
     *
     * @param array<string, mixed>|bool $data Data baris hasil query (bisa berupa false jika query kosong).
     * @return array<string, mixed>|bool Data yang sudah disensor, atau false jika input juga false.
     */
    protected function filterHidden(array|bool $data): array|bool
    {
        if (!$data || empty($this->hidden)) {
            return $data;
        }
        foreach ($this->hidden as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    /**
     * Mempersiapkan data sebelum masuk database (Mutator).
     * Mengubah array menjadi string JSON jika di-cast sebagai 'json'.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function prepareCasts(array $data): array
    {
        foreach ($this->casts as $key => $type) {
            if (
                isset($data[$key]) && $type === 'json' && is_array($data[$key])
                || is_object($data[$key])
            ) {
                $data[$key] = json_encode($data[$key]);
            }
        }
        return $data;
    }

    /**
     * Mengubah data string dari database menjadi tipe data asli (Accessor).
     * Mengubah string JSON menjadi array asosiatif.
     *
     * @param array<string, mixed>|bool $data
     * @return array<string, mixed>|bool
     */
    protected function applyCasts(array|bool $data): array|bool
    {
        if (!$data) {
            return $data;
        }

        foreach ($this->casts as $key => $type) {
            if (isset($data[$key]) && $type === 'json' && is_string($data[$key])) {
                // Decode ke array asosiatif
                $data[$key] = json_decode($data[$key], true) ?? [];
            }
        }
        return $data;
    }

    /**
     * Helper internal untuk memproses output data (Hidden & Casts)
     */
    protected function processOutput(array|bool $data): array|bool
    {
        $data = $this->filterHidden($data);
        return $this->applyCasts($data);
    }

    /**
     * Mengambil seluruh data dari tabel model bersangkutan.
     *
     * @return array<int, array<string, mixed>> Kumpulan seluruh baris data dalam bentuk array asosiatif tersensor.
     */
    public function findAll(): array
    {
        $results = $this->query()->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }

    /**
     * Mencari dan mengambil satu baris data berdasarkan Primary Key.
     *
     * @param int|string $id Nilai ID yang dicari.
     * @return array<string, mixed>|bool Data baris yang ditemukan (tersensor), atau FALSE jika tidak ada.
     */
    public function findById(int|string $id): array|bool
    {
        $result = $this->query()
            ->where($this->primaryKey, '=', $id)
            ->first();

        return $this->processOutput($result);
    }

    /**
     * Menyimpan data baru (Insert) ke dalam tabel.
     * Otomatis memvalidasi Mass Assignment dan menyuntikkan Timestamp.
     *
     * @param array<string, mixed> $data Array asosiatif dengan format ['nama_kolom' => 'nilai'].
     * @return bool TRUE jika penyimpanan sukses, FALSE jika gagal.
     */
    public function create(array $data): bool
    {
        $data = $this->filterFillable($data);
        $data = $this->setTimestamps($data, false);
        $data = $this->prepareCasts($data);

        return $this->query()->insert($data);
    }

    /**
     * Memperbarui data (Update) di dalam tabel berdasarkan Primary Key.
     * Otomatis memvalidasi Mass Assignment dan memperbarui Timestamp 'updated_at'.
     *
     * @param int|string $id Target ID baris yang akan diperbarui.
     * @param array<string, mixed> $data Array asosiatif yang menampung data baru ['kolom' => 'nilai_baru'].
     * @return bool TRUE jika pembaruan sukses, FALSE jika gagal.
     */
    public function update(int|string $id, array $data): bool
    {
        $data = $this->filterFillable($data);
        $data = $this->setTimestamps($data, true);
        $data = $this->prepareCasts($data);

        return $this->query()
            ->where($this->primaryKey, '=', $id)
            ->update($data);
    }

    /**
     * Menghapus data (Delete) dari tabel berdasarkan Primary Key.
     *
     * @param int|string $id Target ID baris yang akan dihapus.
     * @return bool TRUE jika penghapusan sukses, FALSE jika gagal.
     */
    public function delete(int|string $id): bool
    {
        return $this->query()
            ->where($this->primaryKey, '=', $id)
            ->delete();
    }
}
