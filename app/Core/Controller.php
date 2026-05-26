<?php

namespace App\Core;

use App\Core\View;

abstract class Controller
{
    /**
     * Helper untuk merender View.
     * Controller turunan cukup memanggil $this->view(...)
     * @param string $viewPath Path ke file View
     * @param object|null $data objek DTO yang berisi data
     */
    protected function view(string $viewPath, ?object $data = null): void
    {
        View::render($viewPath, $data);
    }

    /**
     * Melakukan redirect header.
     * * @param string $url URL tujuan
     */
    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

}
