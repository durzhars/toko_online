<?php

namespace App\Core;

/**
 * Kelas Router
 * Bertugas mencocokkan URL pengguna dengan Controller dan Method yang tepat.
 */
class Router
{
    private array $routes = [];

    /**
     * Mendaftarkan Rute dengan metode GET
     */
    public function get(string $path, string $controllerAction): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'action' => $controllerAction,
        ];
    }

    /**
     * Mendaftarkan Rute dengan metode POST
     */
    public function post(string $path, string $controllerAction): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $path,
            'action' => $controllerAction,
        ];
    }

    /**
     * Menjalankan Router, mencari kecocokan URL, dan memanggil Controller.
     */
    public function dispatch(): void
    {
        $url = isset($_GET['url']) ? '/' . rtrim($_GET['url'], '/') : '/';
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $url) {
                [$controller, $action] = explode('@', $route['action']);
                $controllerClass = "App\\Controller\\" . $controller;
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $action)) {
                        $controllerInstance->$action();
                        return;
                    }
                }
            }
        }
        http_response_code(404);
        echo "<h1> 404 - Halaman Tidak Ditemukan </h1>";
    }
}
