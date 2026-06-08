<?php

use App\Core\Helper;

/**
 * @var string $title
 * @var string $content (Diinjeksi otomatis oleh ViewBuilder)
 * @var string $user_name
 * @var int $cart_count
 */
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Toko Online') ?></title>

    <link rel="stylesheet" href="<?= Helper::url('css/global.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/buttons.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/navbar.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/katalog.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/transaksi.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/auth.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/toast.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/modal.css') ?>">
    <link rel="stylesheet" href="<?= Helper::url('css/detail.css') ?>">
</head>

<body>
    <?php require __DIR__ . '/../resources/navbar.php'; ?>

    <?php require __DIR__ . '/../resources/toast.php'; ?>

    <?php require __DIR__ . '/../resources/modal.php'; ?>

    <main>
        <?= $content ?>
    </main>

    <script src="<?= Helper::url('scripts/app.js') ?>"></script>
</body>

</html>
