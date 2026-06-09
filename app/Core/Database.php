<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

/**
 * Kelas Wrapper Database.
 * Menangani koneksi ke database menggunakan PDO, menerapkan pola Koneksi Persisten,
 * serta menyediakan antarmuka Prepared Statements yang aman dari SQL Injection.
 */
class Database
{
    /** @var PDO Objek koneksi PDO utama */
    private PDO $pdo;

    /** @var PDOStatement|false Objek statement untuk eksekusi query */
    private PDOStatement|false $stmt;

    /** @var string Menyimpan pesan error jika koneksi gagal */
    private string $error;

    /**
     * Inisialisasi koneksi saat kelas diinstansiasi.
     * Mengambil kredensial dari file .env melalui kelas Helper.
     */
    public function __construct()
    {
        $host = Helper::env('DB_HOST', 'localhost');
        $dbName = Helper::env('DB_NAME', 'toko_online');
        $user = Helper::env('DB_USER', 'root');
        $pass = Helper::env('DB_PASS', '');

        $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
        $opts = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $opts);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die('Koneksi Database Gagal: ' . $this->error);
        }
    }

    /**
     * Mempersiapkan query SQL untuk dieksekusi.
     *
     * @param string $query Sintaks SQL mentah dengan placeholder (misal: SELECT * FROM t WHERE id = :id).
     * @return void
     */
    public function query(string $query): void
    {
        $this->stmt = $this->pdo->prepare($query);
    }

    /**
     * Mengikat nilai ke dalam parameter query secara aman.
     * Tipe data akan dideteksi secara otomatis jika tidak didefinisikan.
     *
     * @param int|string $param Nama parameter (contoh: ':id' atau index 1).
     * @param mixed      $value Nilai yang akan dimasukkan.
     * @param int|null   $type  Konstanta tipe data PDO (contoh: PDO::PARAM_INT).
     * @return void
     */
    public function bind(int|string $param, mixed $value, ?int $type = null): void
    {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Menjalankan statement query yang sudah disiapkan.
     *
     * @return bool True jika eksekusi berhasil.
     */
    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    /**
     * Mengambil seluruh baris data dari hasil eksekusi query.
     *
     * @return array<int, array<string, mixed>> Array asosiatif baris data.
     */
    public function resultSet(): array
    {
        $this->execute();
        return $this->stmt->fetchAll() ?: [];
    }

    /**
     * Mengambil satu baris data tunggal dari hasil eksekusi query.
     *
     * @return array<string, mixed>|bool Array asosiatif baris data, atau FALSE jika tidak ditemukan.
     */
    public function single(): array|bool
    {
        $this->execute();
        return $this->stmt->fetch();
    }

    /**
     * Memulai transaksi database (ACID Compliance).
     *
     * @return bool True jika berhasil dimulai.
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Menyimpan seluruh perubahan data pada transaksi secara permanen.
     *
     * @return bool True jika berhasil di-commit.
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Membatalkan seluruh perubahan data pada transaksi yang sedang berjalan.
     *
     * @return bool True jika berhasil di-rollback.
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }
}
