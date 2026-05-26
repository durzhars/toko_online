<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Kelas Wrapper Database
 * Menangani koneksi MariaDB menggunakan PDO dan Prepared Statements.
 */
class Database
{
    private PDO $pdo;
    private PDOStatement $stmt;
    private string $error;

    /**
     * Inisialisasi koneksi saat kelas dipanggil.
     * Mengambil kredensial dari file .env di root direktori.
     */
    public function __construct()
    {
        $env = parse_ini_file(__DIR__ . '/../../.env');
        $dsn = 'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4';

        $opts = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], $opts);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Koneksi Database Gagal: " . $this->error);
        }
    }

    /**
     * Mempersiapkan query SQL untuk dieksekusi.
     * @param string $query Sintaks SQL yang akan dieksekusi
     */
    public function query(string $query): void
    {
        $this->stmt = $this->pdo->prepare($query);
    }

    /**
     * Mengikat nilai ke dalam parameter query.
     * @param int|string $param Nama parameter (contoh: ':id' atau 1)
     * @param mixed $value Nilai yang akan dimasukkan
     * @param int|null $type Tipe data PDO (Otomatis dideteksi jika null)
     */
    public function bind(int|string $param, mixed $value, $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Menjalankan statement query yang sudah disiapkan.
     * @return bool TRUE jika sukses, FALSE jika gagal
     */
    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    /**
     * Mengambil seluruh baris data dari hasil eksekusi query.
     *  @return array mengembalikan array berisi semua baris yang tersisa di result set.
     *  Bisa sebagai baris array dari nilai kolom atau sebuah objek dengan
     *  properties yang terkait dengan nama kolom tsb. Return 0 jika tidak ada
     *  hasil yang di ambil
     */
    public function resultSet(): array
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    /**
     * Mengambil satu baris data tunggal dari hasil eksekusi query.
     */
    public function single(): array|bool
    {
        $this->execute();
        return $this->stmt->fetch();
    }
}
