<?php
declare(strict_types=1);
final class AdminOrderRepository
{
 public function __construct(private readonly PDO $db){}
 public function all():array{return $this->db->query("SELECT DISTINCT ON(p.id_pedido) p.id_pedido,p.fecha_pedido,p.total,p.estado,CONCAT(u.nombre,' ',u.apellido) cliente,pa.metodo,pa.estado pago_estado FROM pedidos p INNER JOIN usuarios u ON u.id_usuario=p.id_usuario LEFT JOIN pagos pa ON pa.id_pedido=p.id_pedido ORDER BY p.id_pedido DESC,pa.fecha_creacion DESC")->fetchAll();}
 public function find(int $id):?array{$q=$this->db->prepare("SELECT p.*,CONCAT(u.nombre,' ',u.apellido) cliente,u.correo,u.telefono,u.direccion,pa.id_pago,pa.metodo,pa.estado pago_estado,pa.monto,pa.referencia_transaccion,pa.fecha_confirmacion,pa.comprobante_archivo FROM pedidos p INNER JOIN usuarios u ON u.id_usuario=p.id_usuario LEFT JOIN pagos pa ON pa.id_pedido=p.id_pedido WHERE p.id_pedido=:id ORDER BY pa.fecha_creacion DESC LIMIT 1");$q->execute(['id'=>$id]);$o=$q->fetch();if(!$o)return null;$d=$this->db->prepare('SELECT d.cantidad,d.precio_unitario,d.subtotal,pr.nombre FROM detalles_pedido d INNER JOIN productos pr ON pr.id_producto=d.id_producto WHERE d.id_pedido=:id ORDER BY d.id_detalle');$d->execute(['id'=>$id]);$o['items']=$d->fetchAll();return $o;}
 public function updateOrder(int $id,string $state):void{$q=$this->db->prepare('UPDATE pedidos SET estado=:estado WHERE id_pedido=:id');$q->execute(['estado'=>$state,'id'=>$id]);}
 public function approvePayment(int $orderId):void{$q=$this->db->prepare("UPDATE pagos SET estado='APROBADO',fecha_confirmacion=CURRENT_TIMESTAMP WHERE id_pedido=:id AND estado='PENDIENTE'");$q->execute(['id'=>$orderId]);if($q->rowCount()<1)throw new RuntimeException('No hay un pago pendiente para aprobar.');}
}
