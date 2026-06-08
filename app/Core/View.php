<?php

namespace App\Core;

/**
 * Kelas View Builder.
 * Bertanggung jawab untuk merangkai data dan merender tampilan HTML.
 * Menggunakan pola Fluent Interface dan Output Buffering.
 */
class View
{
    /** @var string Path menuju file View (tanpa ekstensi .php) */
    private string $viewPath;

    /** @var array<string, mixed> Kumpulan data yang diinjeksi ke View */
    private array $data = [];

    /** @var string|null Nama layout master yang digunakan (null jika tanpa layout) */
    private ?string $layout = 'app';

    /**
     * Konstruktor kelas View.
     *
     * @param string $viewPath Path relatif dari folder View (contoh: 'user/katalog').
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Mengatur judul halaman (Title).
     *
     * @param string $title Judul halaman.
     * @return self
     */
    public function title(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Menginjeksi data atau variabel dari Controller ke View.
     *
     * @param string|array<string, mixed> $key   Nama variabel, atau array asosiatif multi-variabel.
     * @param mixed                       $value Nilai variabel (diabaikan jika $key adalah array).
     * @return self
     */
    public function with(string|array $key, mixed $value = null): self
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Menetapkan layout (master template) pembungkus View ini.
     *
     * @param string|null $layoutName Nama file layout di folder View/layout/ (isi null untuk menghilangkan layout).
     * @return self
     */
    public function layout(?string $layoutName): self
    {
        $this->layout = $layoutName;
        return $this;
    }

    /**
     * Mengeksekusi dan merender tampilan ke browser, lalu menghentikan skrip.
     * Data sesi global otomatis diinjeksi di sini.
     *
     * @return never
     */
    public function render(): void
    {
        // Injeksi data global yang selalu dibutuhkan layout
        $this->data['user_name'] = Auth::user('nama') ?? 'Tamu';
        $this->data['is_admin'] = Auth::isAdmin();
        $this->data['cart_count'] = isset($_SESSION['keranjang']) ? array_sum($_SESSION['keranjang']) : 0;

        extract($this->data);
        $file = __DIR__ . '/../View/' . str_replace('.', '/', $this->viewPath) . '.php';

        if (!file_exists($file)) {
            die("View Error: File '{$this->viewPath}' tidak ditemukan di lokasi '$file'");
        }

        // Render langsung jika tidak menggunakan layout
        if ($this->layout === null) {
            require_once $file;
            exit;
        }

        // Gunakan Output Buffering jika menggunakan layout
        ob_start();
        require_once $file;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../View/layout/' . str_replace('.', '/', $this->layout) . '.php';

        if (file_exists($layoutFile)) {
            require_once $layoutFile;
        } else {
            die("Layout Error: File '{$this->layout}' tidak ditemukan di '$layoutFile'");
        }
        exit;
    }
}
