<?php

declare(strict_types=1);

class DashboardRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public function ventasAprobadasEntre(DateTimeImmutable $desde, DateTimeImmutable $hasta): string
    {
        $statement = $this->connection->prepare(
            "SELECT COALESCE(SUM(monto), 0) AS total
             FROM pagos
             WHERE estado = 'APROBADO'
               AND fecha_confirmacion >= :desde
               AND fecha_confirmacion < :hasta"
        );
        $statement->execute([
            'desde' => $desde->format(DateTimeInterface::ATOM),
            'hasta' => $hasta->format(DateTimeInterface::ATOM),
        ]);

        return (string) $statement->fetchColumn();
    }

    public function contarPedidosPendientes(): int
    {
        $statement = $this->connection->prepare(
            "SELECT COUNT(*) FROM pedidos WHERE estado = :estado"
        );
        $statement->execute(['estado' => 'PENDIENTE']);

        return (int) $statement->fetchColumn();
    }

    public function contarProductosConStockBajo(int $limite): int
    {
        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM productos
             WHERE activo = TRUE AND stock > 0 AND stock <= :limite'
        );
        $statement->bindValue(':limite', $limite, PDO::PARAM_INT);
        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    public function pedidosRecientes(int $limite): array
    {
        $statement = $this->connection->prepare(
            'SELECT p.id_pedido,
                    CONCAT(u.nombre, \' \', u.apellido) AS cliente,
                    p.fecha_pedido,
                    p.total,
                    p.estado
             FROM pedidos p
             INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
             ORDER BY p.fecha_pedido DESC, p.id_pedido DESC
             LIMIT :limite'
        );
        $statement->bindValue(':limite', $limite, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProductosActivos(): int
    {
        return (int) $this->connection->query('SELECT COUNT(*) FROM productos WHERE activo=TRUE')->fetchColumn();
    }

    public function ventasDiarias(DateTimeImmutable $desde, DateTimeImmutable $hasta, string $timezone): array
    {
        $q=$this->connection->prepare("SELECT TO_CHAR(fecha_confirmacion AT TIME ZONE :timezone,'YYYY-MM-DD') AS fecha, SUM(monto) AS total FROM pagos WHERE estado='APROBADO' AND fecha_confirmacion>=:desde AND fecha_confirmacion<:hasta GROUP BY 1 ORDER BY 1");
        $q->execute(['timezone'=>$timezone,'desde'=>$desde->format(DateTimeInterface::ATOM),'hasta'=>$hasta->format(DateTimeInterface::ATOM)]);return $q->fetchAll();
    }

    public function estadosPedidos(): array
    {
        return $this->connection->query('SELECT estado,COUNT(*) AS cantidad FROM pedidos GROUP BY estado ORDER BY estado')->fetchAll();
    }

    public function productosStockBajo(int $limite,int $cantidad=5): array
    {
        $q=$this->connection->prepare('SELECT p.id_producto,p.nombre,p.stock,p.imagen,c.nombre AS categoria FROM productos p INNER JOIN categorias c ON c.id_categoria=p.id_categoria WHERE p.activo=TRUE AND p.stock>0 AND p.stock<=:limite ORDER BY p.stock,p.nombre LIMIT :cantidad');$q->bindValue(':limite',$limite,PDO::PARAM_INT);$q->bindValue(':cantidad',$cantidad,PDO::PARAM_INT);$q->execute();return $q->fetchAll();
    }
}
