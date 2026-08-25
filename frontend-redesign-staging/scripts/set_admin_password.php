<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require_once $root . '/config/database.php';

$email = strtolower(trim((string) getenv('ADMIN_EMAIL')));
$password = (string) getenv('ADMIN_PASSWORD');
if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    fwrite(STDERR, "ADMIN_EMAIL must be a valid email address.\n");
    exit(1);
}
if (strlen($password) < 12) {
    fwrite(STDERR, "ADMIN_PASSWORD must contain at least 12 characters.\n");
    exit(1);
}

$connection = (new CConexion())->conexionBD();
$statement = $connection->prepare(
    'UPDATE usuarios
     SET contrasena_hash = :password_hash
     WHERE LOWER(correo) = :email AND rol = :role AND activo = TRUE'
);
$statement->execute([
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'email' => $email,
    'role' => 'ADMIN',
]);

if ($statement->rowCount() !== 1) {
    fwrite(STDERR, "No active admin password was changed; verify that the ADMIN user exists.\n");
    exit(1);
}

fwrite(STDOUT, "Admin password hash updated successfully.\n");
