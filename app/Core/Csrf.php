<?php
namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_token'])) $_SESSION['_token'] = bin2hex(random_bytes(32));
        return $_SESSION['_token'];
    }

    public static function verify(): void
    {
        $token = $_POST['_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['_token'] ?? '', $token)) {
            http_response_code(419);
            die('Token CSRF inválido. Regresa a la página anterior e inténtalo nuevamente.');
        }
    }
}
