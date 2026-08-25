<?php

declare(strict_types=1);

final class Category
{
    public function __construct(private readonly ?PDO $connection)
    {
    }

    public function all(): array
    {
        if ($this->connection === null) {
            return [];
        }

        $statement = $this->connection->query(
            'SELECT c.id_categoria, c.nombre, c.descripcion, COUNT(p.id_producto) AS productos_count
             FROM categorias c
             LEFT JOIN productos p ON p.id_categoria = c.id_categoria AND p.activo = TRUE
             WHERE c.activo = TRUE
             GROUP BY c.id_categoria, c.nombre, c.descripcion
             ORDER BY c.nombre ASC'
        );

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
