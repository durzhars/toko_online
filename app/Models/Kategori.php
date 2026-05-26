<?php

namespace App\Models;

use App\Core\Model;

class Kategori extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = 'kategori';
    }
}
