# 🛒 Kyushop - E-Commerce MVC Framework

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-Compatible-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-Native-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![Vanilla JS](https://img.shields.io/badge/Vanilla_JS-ES6-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

Toko Online adalah aplikasi *e-commerce* berbasis web yang dibangun sepenuhnya dari nol menggunakan arsitektur **Native MVC (Model-View-Controller)**. Proyek ini tidak bergantung pada *framework* eksternal seperti Laravel atau CodeIgniter, melainkan menggunakan mesin *framework* buatan sendiri untuk mendemonstrasikan pemahaman mendalam mengenai pola desain perangkat lunak, *routing*, dan interaksi database.

## ✨ Fitur Utama

### 🛠️ Keunggulan Teknis (*Engine Core*)
- **Native PSR-4 Autoloader:** Memuat kelas secara otomatis tanpa memerlukan Composer di *production environment*.
- **Fluent Query Builder:** Abstraksi database tingkat lanjut yang mendukung *Method Chaining* (SQL Injection *safe* via PDO Binding).
- **Model Security:** Dilengkapi perlindungan *Mass Assignment* (`$fillable`), *Attribute Hiding*, *Data Casting*, dan *Timestamps* otomatis.
- **View Builder & Master Layouts:** Sistem *templating* dinamis menggunakan *Output Buffering* (memisahkan layout utama dengan *view* parsial).
- **Front-Controller Routing:** Mendukung *URL parameters* dinamis berbasis Regex dan perlindungan method (*Spoofing*).

### 👥 Fitur Pengguna (Pelanggan)
- **Katalog Produk:** Pencarian dan filter kategori produk yang intuitif.
- **Keranjang Belanja (AJAX):** Menambah dan menghapus barang dari keranjang tanpa *reload* halaman menggunakan *Fetch API*.
- **Manajemen Profil & Buku Alamat:** Pengguna dapat menyimpan banyak alamat dan menentukan alamat utama.
- **Checkout & Payment Gateway (Mock):** Simulasi pemrosesan pembayaran dan pemotongan stok menggunakan **Database Transactions (ACID)**.

### 👑 Fitur Administrator
- **Dashboard:** Ringkasan dan kontrol penuh atas aplikasi.
- **CRUD Produk & Kategori:** Manajemen data barang beserta unggahan gambar yang terenkapsulasi.
- **Manajemen Pesanan:** Memperbarui status resi dan memantau pesanan masuk.
- **Laporan Penjualan:** Filter transaksi berdasarkan rentang tanggal dan status, serta fitur **Eksport ke CSV**.

---

## 🚀 Panduan Instalasi (Khusus XAMPP)

Aplikasi ini sangat kompatibel dengan lingkungan **XAMPP** standar (PHP 8.x dan MariaDB/MySQL 10.x).

### 1. Persiapan Direktori
1. Unduh atau *Clone* repositori ini.
2. Pindahkan folder proyek ke dalam direktori `htdocs` XAMPP Anda.
   *(Contoh: `C:\xampp\htdocs\toko_online`)*

### 2. Konfigurasi Environment
1. Salin file `.env.example` dan ubah namanya menjadi `.env`.
2. Sesuaikan konfigurasi di dalamnya. Pastikan `APP_URL` menunjuk ke folder `public`:
   ```ini
   # Jika menggunakan XAMPP tanpa VirtualHost:
   APP_URL=http://localhost/toko_online/public
   APP_DEBUG=true

   DB_HOST=localhost
   DB_NAME=toko_online
   DB_USER=root
   DB_PASS= 
   ```
### 3. Setup Database (Migrasi & Seeding)

Aplikasi ini sudah dilengkapi dengan berkas penyesuaian skema yang kompatibel mundur (backward-compatible) dengan MariaDB 10.x.
1. Buka phpMyAdmin (http://localhost/phpmyadmin).
2. Buat database baru bernama toko_online.
3. Buka Command Prompt (CMD) atau Terminal di dalam folder toko_online.
4. Jalankan perintah berikut untuk mengimpor skema dan mengisi data dummy:
    ```bash
    php seeder.php
    ```
*(Catatan: Jika php tidak dikenali di terminal, Anda bisa mengimpor database/schema.sql secara manual melalui phpMyAdmin, lalu mengakses http://localhost/toko_online/seeder.php via browser sekali saja untuk mengisi data).*

### 4. Jalankan Aplikasi
Buka peramban web Anda dan akses:
👉 http://localhost/toko_online/public

---

## 🔐 Akun Demo
Gunakan kredensial berikut untuk menguji sistem setelah database berhasil di-seed:
| Role | Email | Password |
| ---- | ----- | -------- |
| Admin | admin@email.com | admin123 |
| Pelanggan | pelanggan@email.com | pelanggan123 |

## 📁 Struktur Direktori Utama
toko_online/
├── app/                  # Logika utama aplikasi
│   ├── Controller/       # Pengendali alur sistem (Admin & User)
│   ├── Core/             # Jantung framework MVC (Router, DB, Model, dll)
│   ├── Models/           # Entitas Database
│   ├── Services/         # Integrasi pihak ketiga (Payment Gateway)
│   └── View/             # Tampilan UI HTML/PHP
├── database/             # Skema SQL mentah
├── public/               # Direktori publik (Front-Controller)
│   ├── assets/           # Gambar statis & Logo
│   ├── css/              # Stylesheets modular
│   ├── scripts/          # Interaktivitas JavaScript (AJAX)
│   ├── uploads/          # Folder tujuan unggahan file pengguna
│   ├── .htaccess         # Aturan URL Rewrite Apache
│   └── index.php         # Entry point aplikasi (Routing & Autoloader)
├── .env                  # Konfigurasi sistem
└── seeder.php            # Skrip otomatisasi pengisian database
