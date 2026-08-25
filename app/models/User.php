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

    public function create(string $nombre,string $apellido,string $email,string $passwordHash):array
    {
        if(!$this->connection instanceof PDO)throw new RuntimeException('Database unavailable.');
        $q=$this->connection->prepare("INSERT INTO usuarios(nombre,apellido,correo,contrasena_hash,rol,activo) VALUES(:nombre,:apellido,:correo,:hash,'CLIENTE',TRUE) RETURNING id_usuario,nombre,apellido,correo,rol,activo");
        $q->execute(['nombre'=>$nombre,'apellido'=>$apellido,'correo'=>$email,'hash'=>$passwordHash]);return $q->fetch(PDO::FETCH_ASSOC);
    }
}
