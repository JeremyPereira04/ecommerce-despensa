<?php

declare(strict_types=1);

final class Advertisement
{
    public function __construct(private readonly ?PDO $connection)
    {
    }

    public function active(): array
    {
        if ($this->connection === null) {
            return [];
        }

        $statement = $this->connection->query(
            'SELECT id_publicidad, imagen, texto_alternativo, orden
             FROM publicidad_portada
             WHERE activo = TRUE AND imagen IS NOT NULL
             ORDER BY orden ASC, id_publicidad ASC'
        );
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
