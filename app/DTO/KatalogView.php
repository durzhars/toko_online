<?php

namespace App\DTO;

class KatalogView
{
    public function __construct(
        public readonly string $title,
        public readonly array $daftarProduk,
    ) {
    }
}
