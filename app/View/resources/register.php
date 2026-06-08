<?php

use App\Core\Helper;

?>
<div class="auth-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Daftar Akun Baru</h2>
    <form action="<?= Helper::url('register') ?>" method="POST">
        <div class="input-grup">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" required placeholder="John Doe">
        </div>
        <div class="input-grup">
            <label>Email</label>
            <input type="email" name="email" required placeholder="email@contoh.com">
        </div>
        <div class="input-grup">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Minimal 6 karakter">
        </div>
        <button type="submit" class="btn-auth" style="background: #4CAF50;">Daftar</button>
    </form>
    <p style="text-align: center; margin-top: 15px;">Sudah punya akun? <a href="<?= Helper::url('login') ?>">Login di sini</a></p>
</div>
