<?php

namespace App\Models;

use App\Core\Model;

class DetailTransaksi extends Model
{
    protected string $table = 'detail_transaksi';
    protected bool $timestamps = true;
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'transaksi_id',
        'produk_id',
        'jumlah',
        'harga_satuan',
    ];

    public function getDetailAll(string $id): array
    {
        $results = $this->query()
            ->select('detail_transaksi.*', 'produk.nama_produk', 'produk.path_gambar')
            ->join('produk', 'detail_transaksi.produk_id = produk.id')
            ->where('transaksi_id', '=', $id)
            ->get();

        return array_map(fn ($row) => $this->processOutput($row), $results);
    }
}
