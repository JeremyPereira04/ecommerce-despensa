<?php

declare(strict_types=1);

$sessionDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ecommerce-admin-auth-test';
if (!is_dir($sessionDirectory)) {
    mkdir($sessionDirectory, 0700, true);
}
session_save_path($sessionDirectory);
session_start();

require_once dirname(__DIR__) . '/app/models/User.php';
require_once dirname(__DIR__) . '/app/helpers/view.php';
require_once dirname(__DIR__) . '/app/helpers/auth.php';

function assert_auth(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('CREATE TABLE usuarios (id_usuario INTEGER PRIMARY KEY, nombre TEXT, apellido TEXT, correo TEXT, contrasena_hash TEXT, rol TEXT, activo INTEGER)');
$insert = $pdo->prepare('INSERT INTO usuarios VALUES (:id, :name, :surname, :email, :hash, :role, :active)');
$password = 'Frase-segura-de-prueba-2026';
$hash = password_hash($password, PASSWORD_DEFAULT);
$rows = [
    [1, 'Admin', 'Activo', 'admin-test@example.com', $hash, 'ADMIN', 1],
    [2, 'Cliente', 'Activo', 'client-test@example.com', $hash, 'CLIENTE', 1],
    [3, 'Admin', 'Inactivo', 'inactive-test@example.com', $hash, 'ADMIN', 0],
];
foreach ($rows as [$id, $name, $surname, $email, $passwordHash, $role, $active]) {
    $insert->execute([
        'id' => $id,
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'hash' => $passwordHash,
        'role' => $role,
        'active' => $active,
    ]);
}

$users = new User($pdo);
$admin = $users->findByEmail('ADMIN-TEST@EXAMPLE.COM');
$client = $users->findByEmail('client-test@example.com');
$inactive = $users->findByEmail('inactive-test@example.com');
$missing = $users->findByEmail('missing-test@example.com');

assert_auth(valid_admin_credentials($admin, 'incorrecta') === false, 'Wrong password must fail.');
assert_auth($missing === null && valid_admin_credentials($missing, $password) === false, 'Unknown email must fail generically.');
assert_auth(valid_admin_credentials($client, $password) === false, 'CLIENT role must fail.');
assert_auth(valid_admin_credentials($inactive, $password) === false, 'Inactive admin must fail.');
assert_auth(valid_admin_credentials($admin, $password) === true, 'Active ADMIN with valid hash must succeed.');
assert_auth(verify_csrf(null) === false, 'Missing CSRF token must fail.');
$csrf = csrf_token();
assert_auth(verify_csrf($csrf), 'Current CSRF token must be accepted.');

$before = session_id();
sign_in_admin($admin);
assert_auth(session_id() !== $before, 'Session ID must be regenerated.');
assert_auth(is_admin_authenticated(), 'Authenticated admin session must be recognized.');
assert_auth(($_SESSION['admin_user']['active'] ?? false) === true, 'Admin session must retain active authorization state.');
$_SESSION['admin_last_activity'] = time() - ADMIN_SESSION_IDLE_TIMEOUT - 1;
assert_auth(!is_admin_authenticated(), 'Expired admin session must be rejected.');
sign_out_admin();
assert_auth(!is_admin_authenticated(), 'Signed-out admin session must be cleared.');

echo "Admin authentication tests passed.\n";
