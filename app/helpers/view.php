<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_base_url(): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $lastSlash = strrpos($scriptName, '/');
    $directory = $lastSlash === false ? '' : substr($scriptName, 0, $lastSlash);

    return $directory === '/' ? '' : rtrim($directory, '/');
}

function url(string $page = 'home', array $parameters = []): string
{
    $query = http_build_query(array_merge(['page' => $page], $parameters));

    return app_base_url() . '/index.php?' . $query;
}

function asset(string $path): string
{
    return app_base_url() . '/' . ltrim($path, '/');
}

function product_image(?string $path): string
{
    if ($path === null || trim($path) === '') {
        return asset('assets/images/product-placeholder.svg');
    }

    $normalized = preg_replace('#^/?(?:public/)?#', '', trim($path));

    return asset($normalized ?: 'assets/images/product-placeholder.svg');
}

function money(int|float|string|null $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' Gs.';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return is_array($messages) ? $messages : [];
}

function render(string $view, array $data = []): void
{
    $viewPath = dirname(__DIR__) . '/views/' . ltrim($view, '/');
    if (!is_file($viewPath)) {
        throw new RuntimeException('La vista solicitada no existe.');
    }

    $data['navigationCategories'] ??= $GLOBALS['navigationCategories'] ?? [];
    $data['cartCount'] ??= array_sum($_SESSION['cart'] ?? []);
    $data['flashMessages'] ??= consume_flashes();
    extract($data, EXTR_SKIP);
    $contentView = $viewPath;

    require dirname(__DIR__) . '/views/layouts/header.php';
}

function render_admin(string $view, array $data = []): void
{
    $viewPath = dirname(__DIR__) . '/views/' . ltrim($view, '/');
    if (!is_file($viewPath)) {
        throw new RuntimeException('La vista administrativa solicitada no existe.');
    }
    extract($data, EXTR_SKIP);
    $contentView = $viewPath;
    require dirname(__DIR__) . '/views/layouts/admin-header.php';
}
