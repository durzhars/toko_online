<?php

namespace App\Core;

use App\Core\Database;

abstract class Model
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findAll(): array
    {
        $this->db->query("SELECT * FROM {$this->table}");
        return $this->db->resultSet();
    }

    public function findById(int|string $id): array|bool
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Menyimpan Data baru ke dalam tabel
     * Menerima array asosiatif ['kolom' => 'nilai']
     * @param array $data array asosiatif
     * @return bool TRUE jika sukses, FALSE jika gagal.
     */
    public function create(array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn ($key) => ":$key", array_keys($data)));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $this->db->query($query);

        foreach ($data as $key => $value) {
            $this->db->bind(":$key", $value);
        }
        return $this->db->execute();
    }
}
