<?php

use App\Models\Kategori;
use App\Models\Produk;

require_once __DIR__ . "/vendor/autoload.php";

echo "Menyiapkan Database Seeder...\n";

$kategoriModel = new Kategori();
$produkModel = new Produk();

$daftarKategori = ['Elektronik', 'Pakaian', 'Baju', 'Perabotan'];
$kategoriIds = [];

echo "Mengisi Tabel Kategori...\n";

foreach ($daftarKategori as $index => $name) {
    $kategoriModel->create([
        'nama_kategori' => $name,
    ]);

    $kategoriIds[] = $index + 1;
}

echo "Mengisi Tabel Produk...\n";

$kataSifat = ['Super', 'Premium', 'Basic', 'Pro', 'Ultra', 'Klasik'];
$kataBenda = ['Laptop', 'Kemeja', 'Novel', 'Blender', 'Sepatu', 'Jam Tangan'];

for ($i = 0; $i < 15; $i++) {
    $namaProduk = $kataSifat[array_rand($kataSifat)] . ' ' . $kataBenda[array_rand($kataBenda)];

    $kategoriId = $kategoriIds[array_rand($kategoriIds)];
    $harga = rand(5, 500) * 10000;
    $stok = rand(0, 100);

    $produkModel->create([
        'kategori_id' => $kategoriId,
        'nama_produk' => $namaProduk,
        'harga' => $harga,
        'stok' => $stok,
        'deskripsi' => "TestDeskripsi"
    ]);
}

echo "Seeding Selesai. 4 Kategori dan 15 Produk disimpan di database.\n";
