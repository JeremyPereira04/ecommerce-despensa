<?php
declare(strict_types=1);
final class CheckoutRepository
{
 public function __construct(private readonly PDO $db){}
 public function createOrder(int $userId,array $cart,string $method,string $address,?string $proofFile=null):int
 {
  $this->db->beginTransaction();try{$ids=array_map('intval',array_keys($cart));if(!$ids)throw new InvalidArgumentException('El carrito está vacío.');$items=[];$total=0.0;foreach($ids as $id){$q=$this->db->prepare('SELECT id_producto,nombre,precio,stock,activo FROM productos WHERE id_producto=:id FOR UPDATE');$q->execute(['id'=>$id]);$p=$q->fetch();$qty=(int)($cart[$id]??0);if(!$p||!in_array($p['activo'],[true,1,'t'],true)||$qty<1||$qty>(int)$p['stock'])throw new RuntimeException('Stock insuficiente para uno de los productos.');$subtotal=(float)$p['precio']*$qty;$total+=$subtotal;$items[]=[$p,$qty,$subtotal];}
   $u=$this->db->prepare('UPDATE usuarios SET direccion=:direccion WHERE id_usuario=:id');$u->execute(['direccion'=>$address,'id'=>$userId]);$q=$this->db->prepare("INSERT INTO pedidos(id_usuario,estado,total) VALUES(:usuario,'PENDIENTE',:total) RETURNING id_pedido");$q->execute(['usuario'=>$userId,'total'=>$total]);$order=(int)$q->fetchColumn();
   $detail=$this->db->prepare('INSERT INTO detalles_pedido(id_pedido,id_producto,cantidad,precio_unitario) VALUES(:pedido,:producto,:cantidad,:precio)');$stock=$this->db->prepare('UPDATE productos SET stock=stock-:cantidad,fecha_actualizacion=CURRENT_TIMESTAMP WHERE id_producto=:producto');foreach($items as [$p,$qty,$subtotal]){$detail->execute(['pedido'=>$order,'producto'=>$p['id_producto'],'cantidad'=>$qty,'precio'=>$p['precio']]);$stock->execute(['cantidad'=>$qty,'producto'=>$p['id_producto']]);}
   $pay=$this->db->prepare("INSERT INTO pagos(id_pedido,metodo,estado,monto,referencia_transaccion,comprobante_archivo) VALUES(:pedido,:metodo,'PENDIENTE',:monto,:referencia,:comprobante)");$pay->execute(['pedido'=>$order,'metodo'=>$method,'monto'=>$total,'referencia'=>'PEDIDO-'.str_pad((string)$order,6,'0',STR_PAD_LEFT),'comprobante'=>$proofFile]);$this->db->commit();return $order;
  }catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}
 }
 public function order(int $id,int $userId):?array{$q=$this->db->prepare('SELECT p.id_pedido,p.fecha_pedido,p.estado,p.total,pa.metodo,pa.estado AS pago_estado FROM pedidos p LEFT JOIN pagos pa ON pa.id_pedido=p.id_pedido WHERE p.id_pedido=:id AND p.id_usuario=:usuario');$q->execute(['id'=>$id,'usuario'=>$userId]);$r=$q->fetch();return is_array($r)?$r:null;}
}
