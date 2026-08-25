<?php
declare(strict_types=1);

final class AdminCatalogController
{
    public function __construct(private readonly AdminCatalogService $service,private readonly string $publicPath){}
    public function guardarProducto(?int $id):never
    {
        require_admin();$this->csrf();
        try{$image=$this->upload();$this->service->guardarProducto($_POST,$id,$image);flash('success',$id?'Producto actualizado.':'Producto agregado.');}
        catch(PDOException $e){error_log('Product persistence: '.$e->getCode());flash('danger',$e->getCode()==='23505'?'El código de barras ya existe.':'No se pudo guardar el producto.');}
        catch(Throwable $e){flash('danger',$e instanceof InvalidArgumentException?$e->getMessage():'No se pudo guardar el producto.');}
        header('Location: '.url($id?'admin-product-edit':'admin-product-create',$id?['id'=>$id]:[]),true,303);exit;
    }
    public function guardarCategoria(?int $id):never{require_admin();$this->csrf();try{$this->service->guardarCategoria($_POST,$id);flash('success',$id?'Categoría actualizada.':'Categoría agregada.');}catch(Throwable $e){flash('danger',$e instanceof InvalidArgumentException?$e->getMessage():'No se pudo guardar la categoría; verificá que el nombre no esté repetido.');}header('Location: '.url('admin-categories'),true,303);exit;}
    public function cambiar(string $type,int $id):never{require_admin();$this->csrf();$type==='producto'?$this->service->cambiarProducto($id):$this->service->cambiarCategoria($id);flash('success','Estado actualizado.');header('Location: '.url($type==='producto'?'admin-products':'admin-categories'),true,303);exit;}
    private function csrf():void{if(!verify_csrf($_POST['csrf_token']??null)){http_response_code(403);exit('Solicitud inválida.');}}
    private function upload():?string
    {
        $f=$_FILES['imagen']??null;if(!is_array($f)||($f['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE)return null;if(($f['error']??1)!==UPLOAD_ERR_OK||($f['size']??0)>3*1024*1024)throw new InvalidArgumentException('La imagen debe pesar menos de 3 MB.');
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;if(!$ext)throw new InvalidArgumentException('Usá una imagen JPG, PNG o WebP.');$dir=$this->publicPath.'/uploads/products';if(!is_dir($dir)&&!mkdir($dir,0755,true))throw new RuntimeException('No se pudo crear la carpeta de imágenes.');$name=bin2hex(random_bytes(16)).'.'.$ext;if(!move_uploaded_file($f['tmp_name'],$dir.'/'.$name))throw new RuntimeException('No se pudo guardar la imagen.');return 'uploads/products/'.$name;
    }
}
