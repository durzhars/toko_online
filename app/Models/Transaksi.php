<?php

namespace App\Models;

use App\Core\Model;
use Exception;

/**
 * Model Transaksi
 * Mengatur entitas transaksi/checkout dan penyimpanannya.
 */
class Transaksi extends Model
{
    /** @var string Nama tabel di database */
    protected string $table = 'transaksi';

    /** @var bool Mengaktifkan timestamps otomatis */
    protected bool $timestamps = true;

    /** @var string Primary Key tabel */
    protected string $primaryKey = 'id';

    /** @var array<string> Daftar kolom yang bisa diisi massal */
    protected array $fillable = [
        'id',
        'user_id',
        'total_tagihan',
        'alamat_pengiriman',
        'status',
        'bukti_bayar',
        'resi_pengiriman'
    ];

    /** @var array<string> Daftar kolom yang bisa di casting. */
    protected array $casts = [
        'alamat_pengiriman' => 'json',
    ];

    /**
     * Mengambil daftar pesanan berdasarkan ID User.
     *
     * @param int $userId ID User terkait.
     * @return array<int, array<string, mixed>> Daftar transaksi.
     */
    public function getByUser(int $userId): array
    {
        $results = $this->query()
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }

    /**
     * Mengambil seluruh data transaksi beserta nama pelanggannya.
     *
     * @return array<int, array<string, mixed>>
     **/
    public function getAllLengkap(?string $keyword = null): array
    {
        $query = $this->query()
            ->select('transaksi.*', 'users.nama as nama_pelanggan')
            ->join('users', 'transaksi.user_id = users.id');

        if (!empty($keyword)) {
            $query->whereSearch([
                'transaksi_id',
                'users.nama',
            ], $keyword);
        }

        $results = $query->orderBy('transaksi.created_at', 'DESC')->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }

    /**
     * Mengeksekusi proses checkout secara utuh menggunakan Database Transaction.
     *
     * Memeriksa stok produk, memotong stok, dan merekam pesanan.
     * Menggunakan $this->db->beginTransaction() di sini, karena manajemen
     * transaksi berada di ranah koneksi database, bukan QueryBuilder.
     *
     * @param int                  $userId    ID pengguna yang melakukan pesanan.
     * @param array               $alamat    Alamat pengiriman.
     * @param array<int|string, int> $keranjang Array keranjang belanja [id_produk => kuantitas].
     * @return string ID Transaksi yang terbentuk (contoh: TRX-2026...).
     * @throws Exception Jika produk tidak ditemukan atau stok tidak mencukupi.
     */
    public function prosesCheckout(int $userId, array $alamat, array $keranjang): string
    {
        $produkModel = new Produk();
        $detailTransaksi = new DetailTransaksi();

        $transaksiId = 'TRX-' . date('Ymd') . '-' . rand(10000, 99999);
        $totalTagihan = 0;

        // Fase 1: Validasi stok dan hitung total tagihan
        foreach ($keranjang as $id => $jumlah) {
            $produk = $produkModel->findById($id);
            if (!$produk) {
                throw new Exception("Produk dengan ID $id tidak ditemukan.");
            }
            if ($produk['stok'] < $jumlah) {
                throw new Exception("Stok untuk produk {$produk['nama_produk']} tidak mencukupi.");
            }
            $totalTagihan += $produk['harga'] * $jumlah;
        }

        // Fase 2: Eksekusi transaksi database
        try {
            $this->db->beginTransaction();

            $this->create([
                'id' => $transaksiId,
                'user_id' => $userId,
                'total_tagihan' => $totalTagihan,
                'alamat_pengiriman' => $alamat,
                'status' => 'PENDING',
            ]);

            foreach ($keranjang as $id => $jumlah) {
                $produk = $produkModel->findById($id);

                $detailTransaksi->create([
                    'transaksi_id' => $transaksiId,
                    'produk_id' => $id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $produk['harga'],
                ]);

                // Kurangi stok
                $produkModel->update($id, ['stok' => $produk['stok'] - $jumlah]);
            }

            $this->db->commit();
            return $transaksiId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Menarik data laporan transaksi berdasarkan tanggal dan status
     *
     * @param string $startDate Tanggal Awal (YYYY-MM-DD)
     * @param string $endDate Tanggal akhir (YYYY-MM-DD)
     * @param string $status Filter status transaksi
     * @return array <int, array<string, mixed>>
     **/
    public function getLaporan(string $startDate, string $endDate, string $status = ''): array
    {
        $qdb = $this->query()
            ->select('transaksi.*', 'users.nama as nama_pelanggan')
            ->join('users', 'transaksi.user_id = users.id')
            ->where('transaksi.created_at', '>=', $startDate . ' 00:00:00')
            ->where('transaksi.created_at', '<=', $endDate . ' 23:59:59', 'AND');
        if ($status != '') {
            $qdb->where('transaksi.status', '=', $status, 'AND');
        }

        $results = $qdb->orderBy('transaksi.created_at', 'DESC')
            ->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }
}
