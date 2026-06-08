<?php

namespace App\Models;

use App\Core\Model;

/**
 * Model Alamat Pengiriman
 * Menangani entitas alamat multispesifik untuk setiap pengguna.
 */
class AlamatPengiriman extends Model
{
    protected string $table = 'alamat_pengiriman';
    protected bool $timestamps = true;
    protected array $fillable = [
        'user_id',
        'label',
        'penerima',
        'no_hp',
        'alamat_lengkap',
        'is_utama',
    ];

    /**
     * Mengambil daftar alamat milik pengguna tertentu.
     *
     * @param int $userId ID Pengguna.
     * @return array<int, array<string, mixed>>
     */
    public function getByUser(int $userId): array
    {
        return $this->query()
            ->where('user_id', '=', $userId)
            ->orderBy('is_utama', 'DESC')
            ->get();
    }

    /**
     * Mereset semua alamat milik pengguna menjadi bukan utama (0).
     *
     * @param int $userId ID Pengguna.
     * @return void
     */
    public function resetUtama(int $userId): void
    {
        $this->query()
            ->where('user_id', '=', $userId)
            ->update(['is_utama' => 0]);
    }
}
