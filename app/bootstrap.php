<?php
session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) return;
    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

function app_config(): array {
    static $config;
    return $config ??= require BASE_PATH . '/config/config.php';
}

function app_base_path(): string {
    $configured = app_config()['base_path'];
    if ($configured !== '') return rtrim($configured, '/');
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $dir = rtrim(str_replace('/index.php', '', $script), '/');
    return $dir === '/' ? '' : $dir;
}

function url(string $path = '/'): string {
    return app_base_path() . '/' . ltrim($path, '/');
}

function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}

function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function csrf_field(): string { return '<input type="hidden" name="_token" value="' . e(\App\Core\Csrf::token()) . '">'; }
function old(string $key, $default = '') { return $_SESSION['old'][$key] ?? $default; }
function error(string $key): ?string { return $_SESSION['errors'][$key] ?? null; }
function flash(string $key, ?string $value = null) {
    if ($value !== null) { $_SESSION['flash'][$key] = $value; return null; }
    $out = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $out;
}
function view(string $name, array $data = []): void {
    extract($data);
    $viewFile = BASE_PATH . '/app/Views/' . $name . '.php';
    if (!is_file($viewFile)) throw new RuntimeException("Vista no encontrada: $name");
    include BASE_PATH . '/app/Views/layouts/header.php';
    include $viewFile;
    include BASE_PATH . '/app/Views/layouts/footer.php';
    unset($_SESSION['old'], $_SESSION['errors']);
}
