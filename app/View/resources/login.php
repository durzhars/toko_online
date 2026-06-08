<?php

use App\Core\Helper;

?>
<div class="auth-container">
    <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>
    <form action="<?= Helper::url('login') ?>" method="POST">
        <div class="input-grup">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Masukkan email anda">
        </div>
        <div class="input-grup">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" class="btn-auth" style="background: #2196F3;">Masuk</button>
    </form>
    <p style="text-align: center; margin-top: 15px;">Belum punya akun? <a href="<?= Helper::url('register') ?>">Daftar sekarang</a></p>
</div>
