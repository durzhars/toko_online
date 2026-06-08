<?php

namespace App\Core;

/**
 * Kelas Response
 * Mengatur dan mengirim balasan HTTP ke klien.
 */
class Response
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /** @var int Kode status HTTP */
    protected int $statusCode = self::HTTP_OK;

    /** @var array<string, string> Kumpulan header HTTP */
    protected array $headers = [];

    /** @var mixed Isi dari respons */
    protected mixed $content = null;

    /**
     * Konstruktor Response.
     *
     * @param mixed $content Teks mentah atau array untuk JSON.
     */
    public function __construct(mixed $content = null)
    {
        $this->content = $content;
    }

    /**
     * Mengatur status HTTP.
     *
     * @param int $code Kode status HTTP.
     * @return self
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Menambahkan header kustom.
     *
     * @param string $name  Nama header.
     * @param string $value Nilai header.
     * @return self
     */
    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Mengeksekusi pengalihan URL (Redirect) menggunakan Header HTTP.
     *
     * @param string $path       URL relatif atau absolut.
     * @param int    $statusCode Status redirect (Default: 302).
     * @return never
     */
    public static function redirect(string $path, int $statusCode = self::HTTP_FOUND): void
    {
        $url = filter_var($path, FILTER_VALIDATE_URL) ? $path : Helper::url($path);

        (new self())->setStatusCode($statusCode)
            ->withHeader('Location', $url)
            ->send();
    }

    /**
     * Mengirimkan header dan body respons ke browser, lalu menghentikan skrip.
     *
     * @param mixed $content Opsional, menimpa konten yang ada.
     * @return never
     */
    public function send(mixed $content = null): void
    {
        $finalContent = $content ?? $this->content;

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        http_response_code($this->statusCode);

        if ($this->statusCode === self::HTTP_FOUND) {
            exit;
        }

        if (is_array($finalContent)) {
            header('Content-Type: application/json');
            echo json_encode($finalContent);
        } else {
            echo $finalContent;
        }
        exit;
    }
}
