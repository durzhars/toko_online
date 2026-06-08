<?php

namespace App\Core;

/**
 * Kelas QueryBuilder
 * Menangani perakitan string kueri SQL secara dinamis dan aman dengan PDO binding.
 */
class QueryBuilder
{
    /** @var Database Instance pembungkus PDO */
    protected Database $db;

    /** @var string Nama tabel utama */
    protected string $table;

    /** @var array<string> Daftar kolom yang di-select */
    protected array $selects = ['*'];

    /** @var array<string> Daftar klausa JOIN */
    protected array $joins = [];

    /** @var array<int, array<string, string>> Daftar kondisi WHERE beserta operator logikanya */
    protected array $wheres = [];

    /** @var array<string, mixed> Parameter binding untuk PDO */
    protected array $bindings = [];

    /** @var string Klausa ORDER BY */
    protected string $orderBy = '';

    /** @var int|null Batas jumlah data yang diambil (LIMIT) */
    protected ?int $limitValue = null;

    /** @var int|null Titik mulai data yang diambil (OFFSET) */
    protected ?int $offsetValue = null;

    /**
     * Konstruktor QueryBuilder.
     *
     * @param Database $db    Instance database.
     * @param string   $table Nama tabel.
     */
    public function __construct(Database $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    /**
     * Menentukan kolom apa saja yang akan diambil.
     *
     * @param string ...$columns Daftar nama kolom.
     * @return self
     */
    public function select(string ...$columns): self
    {
        $this->selects = empty($columns) ? ['*'] : $columns;
        return $this;
    }

    /**
     * Menambahkan klausa JOIN ke dalam kueri.
     *
     * @param string $table     Tabel tujuan join.
     * @param string $condition Kondisi join (contoh: 'a.id = b.a_id').
     * @param string $type      Tipe join (INNER, LEFT, RIGHT). Default: INNER.
     * @return self
     */
    public function join(string $table, string $condition, string $type = 'INNER'): self
    {
        $this->joins[] = "$type JOIN $table ON $condition";
        return $this;
    }

    /**
     * Menambahkan kondisi WHERE standar.
     *
     * @param string $column   Nama kolom.
     * @param string $operator Operator perbandingan (=, <, >, LIKE, dll).
     * @param mixed  $value    Nilai yang akan di-bind.
     * @param string $boolean  Operator logika (AND / OR). Default: AND.
     * @return self
     */
    public function where(string $column, string $operator, mixed $value, string $boolean = 'AND'): self
    {
        $paramName = ':' . str_replace('.', '_', $column) . '_' . count($this->bindings);

        $this->wheres[] = [
            'sql' => "$column $operator $paramName",
            'boolean' => strtoupper($boolean)
        ];

        $this->bindings[$paramName] = $value;
        return $this;
    }

    /**
     * Menambahkan kondisi WHERE LIKE secara ringkas.
     *
     * @param string $column  Nama kolom.
     * @param string $val     Nilai pencarian.
     * @param string $boolean Operator logika (AND / OR).
     * @return self
     */
    public function whereLike(string $column, string $val, string $boolean = 'AND'): self
    {
        return $this->where($column, 'LIKE', "%{$val}%", $boolean);
    }

    /**
     * Menambahkan kondisi pencarian multi-kolom yang digabung dengan OR secara otomatis.
     *
     * @param array<string> $columns Daftar kolom yang ingin dicari.
     * @param string        $keyword Kata kunci pencarian.
     * @return self
     */
    public function whereSearch(array $columns, string $keyword): self
    {
        $paramName = ':search_keyword_' . count($this->bindings);
        $this->bindings[$paramName] = "%{$keyword}%";

        $orConditions = [];
        foreach ($columns as $column) {
            $orConditions[] = "$column LIKE $paramName";
        }

        $this->wheres[] = [
            'sql' => '(' . implode(' OR ', $orConditions) . ')',
            'boolean' => 'AND'
        ];

        return $this;
    }

    /**
     * Menentukan urutan hasil (ORDER BY).
     *
     * @param string $column    Nama kolom.
     * @param string $direction Arah urutan (ASC / DESC).
     * @return self
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        // BUG FIX: Tambahkan spasi setelah nama kolom
        $this->orderBy = "ORDER BY $column " . strtoupper($direction);
        return $this;
    }

    /**
     * Membatasi jumlah data yang diambil.
     *
     * @param int $limit  Jumlah maksimal data.
     * @param int $offset Jumlah data yang dilewati (opsional).
     * @return self
     */
    public function limit(int $limit, int $offset = 0): self
    {
        $this->limitValue = $limit;
        if ($offset > 0) {
            $this->offsetValue = $offset;
        }
        return $this;
    }

    /**
     * Helper internal untuk merakit klausa WHERE.
     *
     * @return string Kueri SQL mentah yang telah dirakit.
     **/
    public function compileWheres(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $whereClauses = [];
        foreach ($this->wheres as $index => $where) {
            $prefix = $index === 0 ? 'WHERE' : $where['boolean'];
            $whereClauses[] = "$prefix {$where['sql']}";
        }
        return ' ' . implode(' ', $whereClauses);
    }

    /**
     * Merakit semua komponen menjadi string SQL utuh.
     *
     * @return string Kueri SQL mentah.
     */
    public function compileQuery(): string
    {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        if (!empty($this->wheres)) {
            $whereClauses = [];
            foreach ($this->wheres as $index => $where) {
                $prefix = $index === 0 ? 'WHERE' : $where['boolean'];
                $whereClauses[] = "$prefix {$where['sql']}";
            }
            $sql .= ' ' . implode(' ', $whereClauses);
        }

        if ($this->orderBy !== '') {
            $sql .= ' ' . $this->orderBy;
        }

        if ($this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
            if ($this->offsetValue !== null) {
                $sql .= ' OFFSET ' . $this->offsetValue;
            }
        }

        return $sql;
    }

    /**
     * Mengeksekusi kueri dan mengambil seluruh hasil.
     *
     * @return array<int, array<string, mixed>> Array dari baris data.
     */
    public function get(): array
    {
        $this->db->query($this->compileQuery());

        foreach ($this->bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->resultSet();
    }

    /**
     * Mengeksekusi kueri dan mengambil satu baris data pertama.
     *
     * @return array<string, mixed>|bool Array data, atau false jika tidak ditemukan.
     */
    public function first(): array|bool
    {
        $this->limit(1);
        $this->db->query($this->compileQuery());

        foreach ($this->bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->single();
    }

    /**
     * Mengeksekusi operasi INSERT ke tabel.
     *
     * @param array<string, mixed> $data Array asosiatif kolom => nilai.
     * @return bool True jika insert berhasil.
     */
    public function insert(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn ($key) => ":$key", array_keys($data)));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $this->db->query($sql);

        // Bind data
        foreach ($data as $key => $value) {
            $this->db->bind(":$key", $value);
        }

        return $this->db->execute();
    }

    /**
     * Mengeksekusi operasi UPDATE ke tabel.
     * Metode ini akan menggunakan kondisi WHERE yang sudah di-set sebelumnya (misal: query()->where(...)->update(...))
     *
     * @param array<string, mixed> $data Array asosiatif kolom => nilai baru.
     * @return bool True jika update berhasil.
     */
    public function update(array $data): bool
    {
        // Hindari UPDATE seluruh tabel secara tidak sengaja.
        if (empty($this->wheres)) {
            throw new \Exception("Aksi UPDATE tanpa klausa WHERE dilarang keras untuk mencegah perubahan data massal.");
        }

        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = :update_$key";
        }
        $setSql = implode(', ', $setClauses);

        $sql = "UPDATE {$this->table} SET $setSql" . $this->compileWheres();
        $this->db->query($sql);

        // Bind data SET
        foreach ($data as $key => $value) {
            $this->db->bind(":update_$key", $value);
        }

        // Bind data dari klausa WHERE
        foreach ($this->bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->execute();
    }

    /**
     * Mengeksekusi operasi DELETE ke tabel.
     * Metode ini akan menggunakan kondisi WHERE yang sudah di-set.
     *
     * @return bool True jika delete berhasil.
     */
    public function delete(): bool
    {
        if (empty($this->wheres)) {
            throw new \Exception("Aksi DELETE tanpa klausa WHERE dilarang keras untuk mencegah penghapusan massal.");
        }

        $sql = "DELETE FROM {$this->table}" . $this->compileWheres();
        $this->db->query($sql);

        foreach ($this->bindings as $param => $value) {
            $this->db->bind($param, $value);
        }

        return $this->db->execute();
    }
}
