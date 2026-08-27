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

    public function categoria(int $id): ?array
    {
        $q=$this->db->prepare('SELECT * FROM categorias WHERE id_categoria=:id');
        $q->execute(['id'=>$id]);
        $row=$q->fetch();
        return is_array($row)?$row:null;
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

    public function guardarCategoria(array $c, ?int $id): int
    {
        if($id===null){$q=$this->db->prepare('INSERT INTO categorias(nombre,descripcion,imagen,activo) VALUES(:nombre,:descripcion,:imagen,:activo) RETURNING id_categoria');$q->execute($c);return (int)$q->fetchColumn();}
        $c['id']=$id;$q=$this->db->prepare('UPDATE categorias SET nombre=:nombre,descripcion=:descripcion,imagen=:imagen,activo=:activo WHERE id_categoria=:id RETURNING id_categoria');$q->execute($c);return (int)$q->fetchColumn();
    }

    public function cambiarCategoria(int $id): void
    {
        $q=$this->db->prepare('UPDATE categorias SET activo=NOT activo WHERE id_categoria=:id');$q->execute(['id'=>$id]);
    }

    public function publicidades(): array
    {
        return $this->db->query('SELECT * FROM publicidad_portada ORDER BY orden ASC, id_publicidad ASC')->fetchAll();
    }

    public function publicidad(int $id): ?array
    {
        $q=$this->db->prepare('SELECT * FROM publicidad_portada WHERE id_publicidad=:id');
        $q->execute(['id'=>$id]);$row=$q->fetch();return is_array($row)?$row:null;
    }

    public function guardarPublicidad(array $advertisement): int
    {
        $q=$this->db->prepare('INSERT INTO publicidad_portada(imagen,texto_alternativo,orden,activo,fecha_actualizacion) VALUES(:imagen,:texto_alternativo,:orden,:activo,CURRENT_TIMESTAMP) RETURNING id_publicidad');
        $q->execute($advertisement);return (int)$q->fetchColumn();
    }

    public function cambiarPublicidad(int $id): void
    {
        $q=$this->db->prepare('UPDATE publicidad_portada SET activo=NOT activo,fecha_actualizacion=CURRENT_TIMESTAMP WHERE id_publicidad=:id');$q->execute(['id'=>$id]);
    }

    public function eliminarPublicidad(int $id): ?array
    {
        $q=$this->db->prepare('DELETE FROM publicidad_portada WHERE id_publicidad=:id RETURNING *');$q->execute(['id'=>$id]);$row=$q->fetch();return is_array($row)?$row:null;
    }
}
