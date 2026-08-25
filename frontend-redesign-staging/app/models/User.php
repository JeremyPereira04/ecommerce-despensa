<?php

declare(strict_types=1);

class User
{
    public function __construct(private readonly ?PDO $connection)
    {
    }

    public function findByEmail(string $email): ?array
    {
        if (!$this->connection instanceof PDO) {
            throw new RuntimeException('Database unavailable.');
        }

        $statement = $this->connection->prepare(
            'SELECT id_usuario, nombre, apellido, correo, contrasena_hash, rol, activo
             FROM usuarios
             WHERE LOWER(correo) = LOWER(:email)
             LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($user) ? $user : null;
    }
}
