<?php

declare(strict_types=1);

const ADMIN_SESSION_IDLE_TIMEOUT = 1800;

function admin_user(): ?array
{
    $user = $_SESSION['admin_user'] ?? null;

    return is_array($user)
        && ($user['role'] ?? null) === 'ADMIN'
        && ($user['active'] ?? null) === true
        ? $user
        : null;
}

function is_admin_authenticated(): bool
{
    if (admin_user() === null) {
        return false;
    }

    $lastActivity = $_SESSION['admin_last_activity'] ?? null;
    if (!is_int($lastActivity) || time() - $lastActivity > ADMIN_SESSION_IDLE_TIMEOUT) {
        return false;
    }

    $_SESSION['admin_last_activity'] = time();

    return true;
}

function valid_admin_credentials(?array $user, string $password): bool
{
    if (!is_array($user)) {
        return false;
    }

    $active = in_array($user['activo'] ?? null, [true, 1, '1', 't', 'true'], true);
    $hash = $user['contrasena_hash'] ?? null;

    return $active
        && ($user['rol'] ?? null) === 'ADMIN'
        && is_string($hash)
        && password_verify($password, $hash);
}

function require_admin(): void
{
    if (is_admin_authenticated()) {
        return;
    }

    unset($_SESSION['admin_user'], $_SESSION['admin_last_activity']);
    $_SESSION['admin_login_notice'] = 'Tu sesión administrativa expiró o no es válida. Iniciá sesión nuevamente.';
    header('Location: ' . url('admin-login'), true, 303);
    exit;
}

function sign_in_admin(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['admin_user'] = [
        'id' => (int) $user['id_usuario'],
        'name' => trim((string) $user['nombre'] . ' ' . (string) $user['apellido']),
        'email' => (string) $user['correo'],
        'role' => 'ADMIN',
        'active' => true,
    ];
    $_SESSION['admin_last_activity'] = time();
    unset($_SESSION['admin_login_attempts'], $_SESSION['admin_login_notice']);
}

function sign_out_admin(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => (bool) $params['secure'],
            'httponly' => (bool) $params['httponly'],
            'samesite' => 'Lax',
        ]);
    }
    session_destroy();
}
