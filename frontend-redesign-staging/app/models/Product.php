<?php

declare(strict_types=1);

final class Product
{
    public function __construct(private readonly ?PDO $connection)
    {
    }

    public function featured(int $limit = 6): array
    {
        if ($this->connection === null) {
            return [];
        }

        $statement = $this->connection->prepare(
            'SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id_categoria = p.id_categoria
             WHERE p.activo = TRUE AND c.activo = TRUE
             ORDER BY p.fecha_creacion DESC, p.id_producto DESC
             LIMIT :limit'
        );
        $statement->bindValue(':limit', max(1, min($limit, 12)), PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function catalog(string $search = '', ?int $categoryId = null, string $sort = 'recent'): array
    {
        if ($this->connection === null) {
            return [];
        }

        $orderBy = match ($sort) {
            'price-asc' => 'p.precio ASC, p.nombre ASC',
            'price-desc' => 'p.precio DESC, p.nombre ASC',
            'name' => 'p.nombre ASC',
            default => 'p.fecha_creacion DESC, p.id_producto DESC',
        };
        $conditions = ['p.activo = TRUE', 'c.activo = TRUE'];
        $parameters = [];

        if ($search !== '') {
            $conditions[] = '(p.nombre ILIKE :search OR COALESCE(p.marca, \'\') ILIKE :search OR COALESCE(p.descripcion, \'\') ILIKE :search)';
            $parameters[':search'] = '%' . $search . '%';
        }
        if ($categoryId !== null) {
            $conditions[] = 'p.id_categoria = :category_id';
            $parameters[':category_id'] = $categoryId;
        }

        $sql = 'SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                INNER JOIN categorias c ON c.id_categoria = p.id_categoria
                WHERE ' . implode(' AND ', $conditions) . '
                ORDER BY ' . $orderBy . '
                LIMIT 60';
        $statement = $this->connection->prepare($sql);
        foreach ($parameters as $name => $value) {
            $statement->bindValue($name, $value, $name === ':category_id' ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        if ($this->connection === null || $id < 1) {
            return null;
        }

        $statement = $this->connection->prepare(
            'SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id_categoria = p.id_categoria
             WHERE p.id_producto = :id AND p.activo = TRUE
             LIMIT 1'
        );
        $statement->execute([':id' => $id]);
        $product = $statement->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    public function related(int $productId, int $categoryId, int $limit = 4): array
    {
        if ($this->connection === null) {
            return [];
        }

        $statement = $this->connection->prepare(
            'SELECT p.*, c.nombre AS categoria_nombre
             FROM productos p
             INNER JOIN categorias c ON c.id_categoria = p.id_categoria
             WHERE p.activo = TRUE
               AND p.id_categoria = :category_id
               AND p.id_producto <> :product_id
             ORDER BY p.fecha_creacion DESC
             LIMIT :limit'
        );
        $statement->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $statement->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $statement->bindValue(':limit', max(1, min($limit, 8)), PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
