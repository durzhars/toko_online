<?php

namespace App\Controller;

use App\Core\Controller;
use App\DTO\KatalogView;

class UserController extends Controller
{
    public function index()
    {
        $viewData = new KatalogView(
            title: 'Halaman Utama',
            pesan: 'Sukses!'
        );

        $this->view('user/katalog', $viewData);
    }
}
