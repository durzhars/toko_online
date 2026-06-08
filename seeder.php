<?php

require_once __DIR__ . "/vendor/autoload.php";

use App\Core\Database;
use App\Models\Users;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\AlamatPengiriman;

echo "Menyiapkan Database Seeder...\n";

// 1. Eksekusi Pembersihan (TRUNCATE) dengan aman
$db = new Database();

echo "Membersihkan data lama dari semua tabel...\n";
$db->query("SET FOREIGN_KEY_CHECKS = 0");
$db->execute();

$db->query("TRUNCATE TABLE detail_transaksi");
$db->execute();
$db->query("TRUNCATE TABLE transaksi");
$db->execute();
$db->query("TRUNCATE TABLE alamat_pengiriman");
$db->execute();
$db->query("TRUNCATE TABLE produk");
$db->execute();
$db->query("TRUNCATE TABLE kategori");
$db->execute();
$db->query("TRUNCATE TABLE users");
$db->execute();

$db->query("SET FOREIGN_KEY_CHECKS = 1");
$db->execute();

// 2. Inisialisasi Model
$userModel = new Users();
$kategoriModel = new Kategori();
$produkModel = new Produk();
$alamatModel = new AlamatPengiriman();

// 3. Seeding Users
echo "Mengisi Tabel Users...\n";
$userModel->create([
    'nama' => 'Administrator',
    'email' => 'admin@email.com',
    'password' => password_hash('admin123', PASSWORD_BCRYPT),
    'no_hp' => '081111111111',
    'role' => 'admin'
]);

$userModel->create([
    'nama' => 'Budi Pelanggan',
    'email' => 'pelanggan@email.com',
    'password' => password_hash('pelanggan123', PASSWORD_BCRYPT),
    'no_hp' => '082222222222',
    'role' => 'pelanggan'
]);

// 4. Seeding Alamat Pengiriman
// Karena di-truncate, ID Pelanggan pasti 2
echo "Mengisi Tabel Alamat Pengiriman...\n";
$alamatModel->create([
    'user_id' => 2,
    'label' => 'Rumah',
    'penerima' => 'Budi Setiawan',
    'no_hp' => '082222222222',
    'alamat_lengkap' => 'Jl. Merdeka No. 45, RT 01/RW 02, Kec. Ilir Timur I, Palembang 30121',
    'is_utama' => 1
]);

$alamatModel->create([
    'user_id' => 2,
    'label' => 'Kampus',
    'penerima' => 'Budi (Fakultas)',
    'no_hp' => '083333333333',
    'alamat_lengkap' => 'Gedung Universitas Indo Global Mandiri Lt. 3, Jl. Jend. Sudirman, Palembang',
    'is_utama' => 0
]);

// 5. Seeding Kategori
echo "Mengisi Tabel Kategori...\n";
$daftarKategori = ['Elektronik', 'Pakaian', 'Buku', 'Perabotan'];
foreach ($daftarKategori as $name) {
    $kategoriModel->create([
        'nama_kategori' => $name,
    ]);
}

// 6. Seeding Produk
echo "Mengisi Tabel Produk...\n";
$kataSifat = ['Super', 'Premium', 'Basic', 'Pro', 'Ultra', 'Klasik'];
$kataBenda = ['Buku', 'Figurine', 'Novel', 'Blender', 'Sepatu', 'Jam Tangan'];

for ($i = 0; $i < 15; $i++) {
    $namaProduk = $kataSifat[array_rand($kataSifat)] . ' ' . $kataBenda[array_rand($kataBenda)];
    $kategoriId = rand(1, 4); // Karena ada 4 kategori (ID 1 sampai 4)
    $harga = rand(5, 500) * 10000;
    $stok = rand(10, 100);

    $produkModel->create([
        'kategori_id' => $kategoriId,
        'nama_produk' => $namaProduk,
        'harga'       => $harga,
        'stok'        => $stok,
        'deskripsi'   => "Ini adalah deskripsi uji coba untuk produk {$namaProduk}. Kualitas terjamin dan siap dikirim.",
        'path_gambar' => '/assets/logo.png'
    ]);
}

echo "=========================================\n";
echo "✅ SEEDING DATABASE SELESAI!\n";
echo "=========================================\n";
echo "- 2 Akun (Admin & Pelanggan)\n";
echo "- 2 Alamat Pengiriman (untuk Pelanggan)\n";
echo "- 4 Kategori\n";
echo "- 15 Produk\n";
echo "-----------------------------------------\n";
echo "Akun Admin     : admin@email.com / admin123\n";
echo "Akun Pelanggan : pelanggan@email.com / pelanggan123\n";
echo "=========================================\n";
