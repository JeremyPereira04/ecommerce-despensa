<?php
declare(strict_types=1);

final class AdminCatalogRepository
{
    public function __construct(private readonly PDO $db) {}

    public function productos(): array
    {
        return $this->db->query('SELECT p.*, c.nombre AS categoria_nombre FROM productos p INNER JOIN categorias c ON c.id_categoria=p.id_categoria ORDER BY p.fecha_creacion DESC, p.id_producto DESC')->fetchAll();
    }

    public function producto(int $id): ?array
    {
        $q=$this->db->prepare('SELECT * FROM productos WHERE id_producto=:id');$q->execute(['id'=>$id]);$row=$q->fetch();return is_array($row)?$row:null;
    }

    public function categorias(): array
    {
        return $this->db->query('SELECT c.*, COUNT(p.id_producto) AS productos_count FROM categorias c LEFT JOIN productos p ON p.id_categoria=c.id_categoria GROUP BY c.id_categoria ORDER BY c.nombre')->fetchAll();
    }

    public function guardarProducto(array $p, ?int $id): int
    {
        if ($id===null) {
            $sql='INSERT INTO productos(id_categoria,nombre,descripcion,codigo_barra,marca,presentacion,unidad_medida,precio,stock,imagen,activo) VALUES(:id_categoria,:nombre,:descripcion,:codigo_barra,:marca,:presentacion,:unidad_medida,:precio,:stock,:imagen,:activo) RETURNING id_producto';
        } else {
            $sql='UPDATE productos SET id_categoria=:id_categoria,nombre=:nombre,descripcion=:descripcion,codigo_barra=:codigo_barra,marca=:marca,presentacion=:presentacion,unidad_medida=:unidad_medida,precio=:precio,stock=:stock,imagen=:imagen,activo=:activo,fecha_actualizacion=CURRENT_TIMESTAMP WHERE id_producto=:id RETURNING id_producto';$p['id']=$id;
        }
        $q=$this->db->prepare($sql);$q->execute($p);return (int)$q->fetchColumn();
    }

    public function cambiarProducto(int $id): void
    {
        $q=$this->db->prepare('UPDATE productos SET activo=NOT activo, fecha_actualizacion=CURRENT_TIMESTAMP WHERE id_producto=:id');$q->execute(['id'=>$id]);
    }

    public function guardarCategoria(array $c, ?int $id): void
    {
        if($id===null){$q=$this->db->prepare('INSERT INTO categorias(nombre,descripcion,activo) VALUES(:nombre,:descripcion,TRUE)');$q->execute($c);return;}
        $c['id']=$id;$q=$this->db->prepare('UPDATE categorias SET nombre=:nombre,descripcion=:descripcion WHERE id_categoria=:id');$q->execute($c);
    }

    public function cambiarCategoria(int $id): void
    {
        $q=$this->db->prepare('UPDATE categorias SET activo=NOT activo WHERE id_categoria=:id');$q->execute(['id'=>$id]);
    }
}
