<?php

use App\Core\Helper;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Panel') ?> - QShop</title>

    <link rel="stylesheet" href="<?= Helper::url('css/global.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/buttons.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/modal.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/admin.css') ?>">
</head>

<body>

    <header class="admin-topnav">
        <div class="topnav-left">
            <button id="sidebarToggle" class="btn btn-dark" style="padding: 5px 12px; font-size: 1.2rem;">☰</button>
            <div class="navbar-brand-container">
                <a href="<?= Helper::url('admin/dashboard') ?>" class="navbar-brand">
                    <img src="<?= Helper::url('assets/logo.png') ?>" alt="QShop Logo" class="brand-logo-img">
                </a>
            </div>
            <span class="page-title-top ml-10" style="color: #888;">| <?= htmlspecialchars($title ?? 'Dashboard') ?></span>
        </div>
        <div class="topnav-right">
            <span class="user-greeting">Halo, <strong><?= htmlspecialchars($user_name ?? 'Administrator') ?></strong></span>
        </div>
    </header>

    <div class="admin-wrapper" id="adminWrapper">

        <aside class="admin-sidebar" id="adminSidebar">
            <ul class="sidebar-menu">
                <li><a href="<?= Helper::url('admin/dashboard') ?>">📊 Dashboard</a></li>
                <li><a href="<?= Helper::url('admin/produk') ?>">📦 Manajemen Produk</a></li>
                <li><a href="<?= Helper::url('admin/kategori') ?>">📁 Kategori Produk</a></li>
                <li><a href="<?= Helper::url('admin/pesanan') ?>">🛒 Pesanan Masuk</a></li>
                <li><a href="<?= Helper::url('admin/laporan') ?>">📈 Laporan Penjualan</a></li>
                <li class="divider"></li>
                <li><a href="<?= Helper::url('katalog') ?>" target="_blank">🌐 Lihat Website</a></li>
                <li><a href="<?= Helper::url('logout') ?>" class="text-danger" data-confirm="Yakin ingin keluar dari Admin Panel?">🚪 Logout</a></li>
            </ul>
        </aside>

        <div class="admin-main">
            <?php require __DIR__ . '/../resources/toast.php'; ?>
            <?php require __DIR__ . '/../resources/modal.php'; ?>

            <main class="admin-content">
                <?= $content ?? '' ?>
            </main>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const wrapper = document.getElementById('adminWrapper');

            toggleBtn.addEventListener('click', function() {
                wrapper.classList.toggle('collapsed');
            });
        });
    </script>
</body>

</html>
