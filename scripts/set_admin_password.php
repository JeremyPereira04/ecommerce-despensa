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

$connection = (new AppDatabaseConnection())->conexionBD();
$passwordHash = password_hash(
    $password,
    defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT
);
$find = $connection->prepare('SELECT id_usuario FROM usuarios WHERE LOWER(correo) = :email LIMIT 1');
$find->execute(['email' => $email]);
$userId = $find->fetchColumn();

if ($userId !== false) {
    $statement = $connection->prepare(
        "UPDATE usuarios
         SET contrasena_hash = :password_hash, rol = 'ADMIN', activo = TRUE
         WHERE id_usuario = :id"
    );
    $statement->execute(['password_hash' => $passwordHash, 'id' => $userId]);
    fwrite(STDOUT, "Admin password hash updated successfully.\n");
    exit(0);
}

$statement = $connection->prepare(
    "INSERT INTO usuarios (nombre, apellido, correo, contrasena_hash, rol, activo)
     VALUES (:nombre, :apellido, :email, :password_hash, 'ADMIN', TRUE)"
);
$statement->execute([
    'nombre' => getenv('ADMIN_NAME') ?: 'Administrador',
    'apellido' => getenv('ADMIN_SURNAME') ?: 'Despensa',
    'email' => $email,
    'password_hash' => $passwordHash,
]);
fwrite(STDOUT, "Admin user created successfully.\n");
