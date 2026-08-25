<?php
declare(strict_types=1);
final class AdminOrderController
{
 private const STATES=['PENDIENTE','CONFIRMADO','PREPARANDO','ENTREGADO','CANCELADO'];
 public function __construct(private readonly AdminOrderRepository $repo,private readonly string $proofPath=''){}
 public function index():void{require_admin();render_admin('admin/orders.php',['pageTitle'=>'Pedidos','adminSection'=>'orders','adminOrders'=>$this->repo->all()]);}
 public function detail():void{require_admin();$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);$o=$id?$this->repo->find((int)$id):null;if(!$o){http_response_code(404);render('errors/404.php');return;}render_admin('admin/order-detail.php',['pageTitle'=>'Pedido #'.$id,'adminSection'=>'orders','order'=>$o]);}
 public function update():never{require_admin();$this->csrf();$id=(int)($_GET['id']??0);$state=(string)($_POST['estado']??'');if(!in_array($state,self::STATES,true)){flash('danger','Estado inválido.');}else{try{$this->repo->updateOrder($id,$state);flash('success','Estado del pedido actualizado.');}catch(Throwable){flash('danger','No se pudo actualizar el pedido.');}}$this->back($id);}
 public function approve():never{require_admin();$this->csrf();$id=(int)($_GET['id']??0);try{$this->repo->approvePayment($id);flash('success','Pago aprobado correctamente.');}catch(Throwable $e){flash('danger',$e->getMessage());}$this->back($id);}
 public function proof():void{require_admin();$id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);$o=$id?$this->repo->find((int)$id):null;$name=is_array($o)?basename((string)($o['comprobante_archivo']??'')):'';$file=$name!==''?$this->proofPath.'/'.$name:'';if($name===''||!is_file($file)){http_response_code(404);exit('Comprobante no encontrado.');}$mime=(new finfo(FILEINFO_MIME_TYPE))->file($file)?:'application/octet-stream';header('Content-Type: '.$mime);header('Content-Disposition: inline; filename="comprobante-'.$id.'.'.pathinfo($name,PATHINFO_EXTENSION).'"');header('X-Content-Type-Options: nosniff');readfile($file);}
 private function csrf():void{if(!verify_csrf($_POST['csrf_token']??null)){http_response_code(403);exit('Solicitud inválida.');}}
 private function back(int $id):never{header('Location: '.url('admin-order',['id'=>$id]),true,303);exit;}
}
