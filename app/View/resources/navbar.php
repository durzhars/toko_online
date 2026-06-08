<?php

use App\Core\Auth;
use App\Core\Helper;

// Ambil keyword pencarian dari URL untuk mengisi value form
$keyword = $_GET['q'] ?? '';
?>
<nav class="main-navbar">
    <div class="navbar-brand-container">
        <a href="<?= Helper::url('katalog') ?>" class="navbar-brand">
            <img src="<?= Helper::url('assets/logo.png') ?>" alt="QShop Logo" class="brand-logo-img">
        </a>
    </div>

    <div class="navbar-search-container">
        <form action="<?= Helper::url('katalog') ?>" method="GET" class="navbar-search-form">
            <input type="text" name="q" class="navbar-search-input" placeholder="Cari barang impianmu di sini..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="navbar-search-btn">🔍</button>
        </form>
    </div>

    <div class="navbar-menu-container">

        <a href="<?= Helper::url('keranjang') ?>" class="navbar-icon-link" title="Keranjang">
            🛒 <span class="badge-cart"><?= $cart_count ?? 0 ?></span>
        </a>

        <a href="<?= Helper::url('pesanan') ?>" class="navbar-icon-link" , title="Pesanan Saya">📦</a>

        <div class="navbar-divider"></div>

        <div class="dropdown-container">
            <button class="hamburger-btn" id="userMenuBtn" aria-label="Menu Pengguna">
                ☰ Menu
            </button>

            <div class="dropdown-menu" id="userDropdown">
                <?php if (Auth::check()): ?>
                    <div class="dropdown-header">
                        <small style="color: #888;">Login sebagai,</small><br>
                        <strong><?= htmlspecialchars($user_name ?? '') ?></strong>
                    </div>

                    <a href="<?= Helper::url('profil') ?>" class="dropdown-item">👤 Profil Akun</a>

                    <?php if (Auth::isAdmin()): ?>
                        <div class="dropdown-divider"></div>
                        <a href="<?= Helper::url('admin/dashboard') ?>" class="dropdown-item text-primary">⚙️ Admin Panel</a>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <a href="<?= Helper::url('logout') ?>" class="dropdown-item text-danger" data-confirm="Yakin ingin keluar?">🚪 Logout</a>
                <?php else: ?>
                    <div class="dropdown-header" style="text-align: center;">
                        <p style="margin: 0 0 10px 0; font-size: 0.9em; color: #666;">Selamat datang di QShop</p>
                        <a href="<?= Helper::url('login') ?>" class="btn btn-primary btn-block btn-sm" style="margin-bottom: 5px;">Masuk</a>
                        <a href="<?= Helper::url('register') ?>" class="btn btn-outline-primary btn-block btn-sm">Daftar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuBtn = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');

        if (menuBtn && dropdown) {
            // Toggle menu saat tombol diklik
            menuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            // Tutup menu jika user mengklik area lain di luar dropdown
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== menuBtn) {
                    dropdown.classList.remove('show');
                }
            });
        }
    });
</script>
