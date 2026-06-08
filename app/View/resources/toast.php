<?php

use App\Core\Session;

$flashTypes = [
    'success' => 'toast-success',
    'error' => 'toast-error',
    'warning' => 'toast-warning',
    'info' => 'toast-info'
];
?>
<div id="toast-container" class="toast-container">
    <?php foreach ($flashTypes as $type => $cssClass): ?>
        <?php if ($msg = Session::getFlash($type)): ?>
            <div class="toast-message <?= $cssClass ?>">
                <span><?= htmlspecialchars($msg) ?></span>
                <button onclick="this.parentElement.remove()" class="toast-close">&times;</button>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
