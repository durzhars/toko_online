<?php

use App\Core\Helper;
use App\Core\Router;

session_start();

require_once __DIR__ . '/../app/Core/Helper.php';

Helper::registerAutoloader();

$isDebug = (Helper::env('APP_DEBUG') == 1) || (Helper::env('APP_DEBUG') === 'true');

if ($isDebug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

$router = new Router();

// ==========================================
// ROUTE AUTENTIKASI (Root Controller)
// ==========================================
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@prosesLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@prosesRegister');
$router->get('/logout', 'AuthController@logout');

// ==========================================
// ROUTE PUBLIK & PENGGUNA (Folder: User/)
// ==========================================
// Katalog
$router->get('/', 'User\ProdukController@katalog');
$router->get('/katalog', 'User\ProdukController@katalog');
$router->get('/katalog/{id}', 'User\ProdukController@show');

// Keranjang Belanja
$router->get('/keranjang', 'User\KeranjangController@index');
$router->post('/keranjang/tambah', 'User\KeranjangController@tambah');
$router->get('/keranjang/hapus/{id}', 'User\KeranjangController@hapus');

// Transaksi & Checkout
$router->get('/checkout', 'User\TransaksiController@checkout');
$router->post('/checkout/proses', 'User\TransaksiController@prosesCheckout');
$router->get('/pesanan', 'User\TransaksiController@pesananSaya');

// Profil & Buku Alamat
$router->get('/profil', 'User\ProfilController@index');
$router->put('/profil', 'User\ProfilController@update');
$router->get('/profil/alamat/tambah', 'User\ProfilController@tambahAlamat');
$router->post('/profil/alamat', 'User\ProfilController@storeAlamat');
$router->delete('/profil/alamat/{id}', 'User\ProfilController@destroyAlamat');
$router->put('/profil/alamat/{id}/utama', 'User\ProfilController@setUtama');

// Simulasi Payment Gateway API
$router->get('/payment/{id}', 'User\PaymentController@index');
$router->post('/payment/{id}/process', 'User\PaymentController@process');

// ==========================================
// ROUTE ADMIN (Folder: Admin/)
// ==========================================
// Dashboard
$router->get('/admin/dashboard', 'Admin\DashboardController@index');

// Manajemen Produk (CRUD Lengkap)
$router->get('/admin/produk', 'Admin\ProdukController@index');
$router->get('/admin/produk/tambah', 'Admin\ProdukController@create');
$router->post('/admin/produk', 'Admin\ProdukController@store');
$router->get('/admin/produk/{id}/edit', 'Admin\ProdukController@edit');
$router->put('/admin/produk/{id}', 'Admin\ProdukController@update');
$router->delete('/admin/produk/{id}', 'Admin\ProdukController@destroy');

// Manajemen Kategori
$router->get('/admin/kategori', 'Admin\KategoriController@index');
$router->get('/admin/kategori/tambah', 'Admin\KategoriController@create');
$router->post('/admin/kategori', 'Admin\KategoriController@store');
$router->get('/admin/kategori/{id}/edit', 'Admin\KategoriController@edit');
$router->put('/admin/kategori/{id}', 'Admin\KategoriController@update');
$router->delete('/admin/kategori/{id}', 'Admin\KategoriController@destroy');

// Manajemen Pesanan
$router->get('/admin/pesanan', 'Admin\TransaksiController@index');
$router->get('/admin/pesanan/{id}', 'Admin\TransaksiController@show');
$router->put('/admin/pesanan/{id}', 'Admin\TransaksiController@updateStatus');

//Laporan
$router->get('/admin/laporan', 'Admin\LaporanController@index');
$router->get('/admin/laporan/export', 'Admin\LaporanController@exportCsv');

// ==========================================
// ROUTE PUBLIK & PENGGUNA (Folder: User/)
// ==========================================

// Eksekusi Router
$router->dispatch();
