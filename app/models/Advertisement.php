<?php

declare(strict_types=1);

final class Advertisement
{
    public function __construct(private readonly ?PDO $connection)
    {
    }

    public function active(): ?array
    {
        if ($this->connection === null) {
            return null;
        }

        $statement = $this->connection->query(
            'SELECT imagen, texto_alternativo
             FROM publicidad_portada
             WHERE id_publicidad = 1 AND activo = TRUE AND imagen IS NOT NULL
             LIMIT 1'
        );
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? $row : null;
    }
}
