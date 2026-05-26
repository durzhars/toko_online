<?php

namespace App\Models;

use App\Core\Model;

class Produk extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'produk';
    }

    public function getByKategori(int $kategoriId): array
    {
        $this->db->query("SELECT * FROM {$this->table} WHERE kategori_id = :kategori_id");
        $this->db->bind(':kategori_id', $kategoriId);
        return $this->db->resultSet();
    }

    public function getProdukLengkap(): array
    {
        $query = "SELECT produk.*, kategori.nama_kategori 
            FROM {$this->table} 
            JOIN kategori ON produk.kategori_id = kategori.id 
            ORDER BY produk.created_at DESC";
        $this->db->query($query);
        return $this->db->resultSet();
    }
}
