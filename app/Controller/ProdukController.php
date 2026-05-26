<?php

namespace App\Controller;

use App\Core\Controller;
use App\DTO\KatalogView;
use App\Models\Produk;

class ProdukController extends Controller
{
    public function katalog(): void
    {
        $produkModel = new Produk();

        $semuaProduk = $produkModel->getProdukLengkap();

        $viewData = new KatalogView(
            title: 'Katalog Produk Toko Online',
            daftarProduk: $semuaProduk,
        );
        $this->view('user/katalog', $viewData);
    }
}
