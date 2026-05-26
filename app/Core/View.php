<?php

namespace App\Core;

class View
{
    /**
     * Helper untuk memuat tampilan (View)
     * @param string $view Nama file view (path relatif dari app/View/)
     * @param object|null $data Data object DTO yang berisi data
     */
    public static function render(string $view, ?object $data = null): void
    {
        $file = __DIR__ . '/../View/' . str_replace('.', '/', $view) . '.php';

        if (file_exists($file)) {
            require_once $file;
        } else {
            die("View '$view' tidak ditemukan di '$file'");
        }
    }

}
