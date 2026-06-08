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
}
