<?php

namespace App\Core;

/**
 * Kelas Router.
 * Bertugas memetakan dan mencocokkan URL (URI) request ke Controller dan Method yang tepat.
 * Mendukung Parameter URL Dinamis (Regex) dan RESTful Method Spoofing.
 */
class Router
{
    /** @var array<int, array<string, string>> Koleksi rute aplikasi yang diregistrasi */
    private array $routes = [];

    /**
     * Helper internal untuk mendaftarkan rute ke dalam koleksi.
     *
     * @param string $method           Metode HTTP (GET, POST, PUT, DELETE).
     * @param string $path             Path URL relatif (contoh: '/edit/{id}').
     * @param string $controllerAction Format aksi 'NamaController@namaMethod'.
     * @return void
     */
    private function addRoute(string $method, string $path, string $controllerAction): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'action' => $controllerAction,
        ];
    }

    /**
     * Mendaftarkan rute untuk HTTP GET.
     *
     * @param string $path             URL path.
     * @param string $controllerAction Aksi controller tujuan.
     * @return void
     */
    public function get(string $path, string $controllerAction): void
    {
        $this->addRoute('GET', $path, $controllerAction);
    }

    /**
     * Mendaftarkan rute untuk HTTP POST.
     *
     * @param string $path             URL path.
     * @param string $controllerAction Aksi controller tujuan.
     * @return void
     */
    public function post(string $path, string $controllerAction): void
    {
        $this->addRoute('POST', $path, $controllerAction);
    }

    /**
     * Mendaftarkan rute untuk HTTP PUT.
     *
     * @param string $path             URL path.
     * @param string $controllerAction Aksi controller tujuan.
     * @return void
     */
    public function put(string $path, string $controllerAction): void
    {
        $this->addRoute('PUT', $path, $controllerAction);
    }

    /**
     * Mendaftarkan rute untuk HTTP DELETE.
     *
     * @param string $path             URL path.
     * @param string $controllerAction Aksi controller tujuan.
     * @return void
     */
    public function delete(string $path, string $controllerAction): void
    {
        $this->addRoute('DELETE', $path, $controllerAction);
    }

    /**
     * Menjalankan mesin Router.
     * Memparsing URL, mendeteksi Method Spoofing, memproses Regex untuk parameter dinamis,
     * dan mengeksekusi Controller yang sesuai.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $url = isset($_GET['url']) ? '/' . rtrim($_GET['url'], '/') : '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Deteksi Method Spoofing
        if ($method === 'POST' && isset($_POST['_method'])) {
            $spoofedMethod = strtoupper($_POST['_method']);
            if (in_array($spoofedMethod, ['PUT', 'PATCH', 'DELETE'])) {
                $method = $spoofedMethod;
            }
        }

        foreach ($this->routes as $route) {
            // Ubah {id} menjadi pattern Regex (?P<id>[a-zA-Z0-9_-]+)
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $route['path']);
            $pattern = "#^{$pattern}$#";

            if ($route['method'] === $method && preg_match($pattern, $url, $matches)) {
                // Ekstrak parameter dinamis dari hasil Regex
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controller, $action] = explode('@', $route['action']);
                $controllerClass = "App\\Controller\\" . $controller;

                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $action)) {
                        // Eksekusi fungsi dan lemparkan parameter dinamis ke dalamnya
                        $controllerInstance->$action(...array_values($params));
                        return;
                    }
                }
            }
        }

        // Tampilkan 404 jika tidak ada rute yang cocok
        $this->render404($url);
    }

    /**
     * Menampilkan tampilan default untuk Error 404 menggunakan View Builder.
     * Mendukung pergantian layout secara dinamis berdasarkan URL (Admin vs User).
     *
     * @param string $url URL yang gagal diproses.
     * @return void
     */
    private function render404(string $url): void
    {
        http_response_code(404);

        // Deteksi secara cerdas apakah user tersesat di area admin atau area publik
        $isAreaAdmin = str_starts_with($url, '/admin');
        $layout = ($isAreaAdmin && Auth::isAdmin()) ? 'admin' : 'app';

        // Panggil view 404 terpusat dengan layout yang sesuai
        (new View('resources/404'))
            ->layout($layout)
            ->title('404 Not Found')
            ->with('url', $url)
            ->render();
    }
}
