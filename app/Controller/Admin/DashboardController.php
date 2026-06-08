<?php

namespace App\Controller\Admin;

use App\Core\Auth;
use App\Core\Controller;

class DashboardController extends Controller
{
    /**
     * Konstruktor Dashboard.
     * Pastikan Middleware Admin aktif.
     */
    public function __construct()
    {
        parent::__construct();
        Auth::requireAdmin();
    }

    /**
     * [GET] Menampilkan halaman beranda Admin Panel.
     *
     * @return void
     */
    public function index(): void
    {
        // Nantinya kita bisa melempar data statistik di sini (Total Pesanan, Total Produk, dll)

        $this->view('admin/dashboard')
            ->layout('admin') // 🚀 Kita akan menggunakan layout khusus admin!
            ->title('Dashboard Admin')
            ->render();
    }
}
