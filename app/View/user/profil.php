<?php

/**
 * @var array<string, mixed> $profil
 * @var array<int, array<string, mixed>> $daftarAlamat
 */
?>
<header class="page-header">
    <h1>Profil Saya</h1>
    <p>Kelola informasi pribadi dan daftar alamat pengiriman Anda di sini.</p>
</header>

<main class="container-sm">
    <?php require __DIR__ . '/partials/profil_form.php'; ?>

    <?php require __DIR__ . '/partials/alamat_list.php'; ?>
</main>
