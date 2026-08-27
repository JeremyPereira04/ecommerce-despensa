<?php
declare(strict_types=1);

final class AdminCatalogController
{
    public function __construct(private readonly AdminCatalogService $service,private readonly string $publicPath){}
    public function guardarProducto(?int $id):never
    {
        require_admin();$this->csrf();
        $newImage=null;
        try{
            $current=$id?$this->service->producto($id):null;
            $newImage=$this->uploadImage('assets/images/products',2*1024*1024);
            $this->service->guardarProducto($_POST,$id,$newImage);
            if($newImage!==null&&!empty($current['imagen'])&&$current['imagen']!==$newImage){$this->deleteManagedFile((string)$current['imagen'],'assets/images/products');$this->deleteManagedFile((string)$current['imagen'],'uploads/products');}
            flash('success',$id?'Producto actualizado.':'Producto agregado.');
        }
        catch(PDOException $e){if($newImage!==null){$this->deleteManagedFile($newImage,'assets/images/products');}error_log('Product persistence: '.$e->getCode());flash('danger',$e->getCode()==='23505'?'El código de barras ya existe.':'No se pudo guardar el producto.');}
        catch(Throwable $e){if($newImage!==null){$this->deleteManagedFile($newImage,'assets/images/products');}flash('danger',$e instanceof InvalidArgumentException?$e->getMessage():'No se pudo guardar el producto.');}
        header('Location: '.url($id?'admin-product-edit':'admin-product-create',$id?['id'=>$id]:[]),true,303);exit;
    }
    public function guardarCategoria(?int $id):never
    {
        require_admin();$this->csrf();
        $newImage=null;
        try{
            $current=$id?$this->service->categoria($id):null;
            $newImage=$this->uploadImage('assets/images/categories',2*1024*1024);
            $this->service->guardarCategoria($_POST,$id,$newImage);
            if($newImage!==null&&!empty($current['imagen'])&&$current['imagen']!==$newImage){$this->deleteManagedFile((string)$current['imagen'],'assets/images/categories');}
            flash('success',$id?'Categoría actualizada correctamente.':'Categoría agregada correctamente.');
        }catch(PDOException $e){
            if($newImage!==null){$this->deleteManagedFile($newImage,'assets/images/categories');}
            error_log('Category persistence: '.$e->getCode());
            flash('danger',$e->getCode()==='23505'?'Ya existe una categoría con ese nombre.':'No se pudo guardar la categoría.');
        }catch(Throwable $e){
            if($newImage!==null){$this->deleteManagedFile($newImage,'assets/images/categories');}
            flash('danger',$e instanceof InvalidArgumentException?$e->getMessage():'No se pudo guardar la categoría.');
        }
        header('Location: '.url('admin-categories'),true,303);exit;
    }
    public function guardarPublicidad():never
    {
        require_admin();$this->csrf();
        $newImage=null;
        try{
            $newImage=$this->uploadImage('assets/images/advertising',5*1024*1024,1200,400,1.8,3.2);
            $this->service->guardarPublicidad($_POST,$newImage);
            flash('success','Publicidad agregada al carrusel correctamente.');
        }catch(Throwable $e){
            if($newImage!==null){$this->deleteManagedFile($newImage,'assets/images/advertising');}
            error_log('Advertisement persistence: '.$e->getMessage());
            flash('danger',$e instanceof InvalidArgumentException?$e->getMessage():'No se pudo agregar la publicidad.');
        }
        header('Location: '.url('admin-settings'),true,303);exit;
    }
    public function cambiarPublicidad(int $id):never
    {
        require_admin();$this->csrf();
        try{$this->service->cambiarPublicidad($id);flash('success','Estado de la publicidad actualizado.');}
        catch(Throwable $e){error_log('Advertisement toggle: '.$e->getMessage());flash('danger','No se pudo actualizar la publicidad.');}
        header('Location: '.url('admin-settings'),true,303);exit;
    }
    public function eliminarPublicidad(int $id):never
    {
        require_admin();$this->csrf();
        try{$deleted=$this->service->eliminarPublicidad($id);if($deleted){$this->deleteManagedFile((string)($deleted['imagen']??''),'assets/images/advertising');}flash('success','Publicidad eliminada.');}
        catch(Throwable $e){error_log('Advertisement delete: '.$e->getMessage());flash('danger','No se pudo eliminar la publicidad.');}
        header('Location: '.url('admin-settings'),true,303);exit;
    }
    public function cambiar(string $type,int $id):never{require_admin();$this->csrf();$type==='producto'?$this->service->cambiarProducto($id):$this->service->cambiarCategoria($id);flash('success','Estado actualizado.');header('Location: '.url($type==='producto'?'admin-products':'admin-categories'),true,303);exit;}
    private function csrf():void{if(!verify_csrf($_POST['csrf_token']??null)){http_response_code(403);exit('Solicitud inválida.');}}
    private function uploadImage(string $relativeDirectory,int $maxBytes,int $minWidth=0,int $minHeight=0,float $minRatio=0,float $maxRatio=0):?string
    {
        $file=$_FILES['imagen']??null;
        if(!is_array($file)||($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE){return null;}
        $error=(int)($file['error']??UPLOAD_ERR_NO_FILE);
        if($error!==UPLOAD_ERR_OK){
            $message=in_array($error,[UPLOAD_ERR_INI_SIZE,UPLOAD_ERR_FORM_SIZE],true)?'La imagen supera el tamaño máximo permitido.':'La imagen no se pudo cargar; volvé a intentarlo.';
            throw new InvalidArgumentException($message);
        }
        $tmp=(string)($file['tmp_name']??'');
        $reportedSize=(int)($file['size']??0);
        $actualSize=is_file($tmp)?filesize($tmp):false;
        if($tmp===''||!is_uploaded_file($tmp)||$actualSize===false){throw new InvalidArgumentException('El archivo recibido no es una carga válida.');}
        if($reportedSize>$maxBytes||$actualSize>$maxBytes){throw new InvalidArgumentException('La imagen debe pesar como máximo '.($maxBytes/1024/1024).' MB.');}
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $extension=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
        if($extension===null){throw new InvalidArgumentException('Seleccioná una imagen JPG, JPEG, PNG o WebP válida.');}
        $dimensions=@getimagesize($tmp);
        if(!is_array($dimensions)){throw new InvalidArgumentException('El archivo no contiene una imagen válida.');}
        $width=(int)$dimensions[0];$height=(int)$dimensions[1];$ratio=$height>0?$width/$height:0;
        if(($minWidth>0&&$width<$minWidth)||($minHeight>0&&$height<$minHeight)){throw new InvalidArgumentException("La publicidad debe medir al menos {$minWidth} × {$minHeight} píxeles.");}
        if(($minRatio>0&&$ratio<$minRatio)||($maxRatio>0&&$ratio>$maxRatio)){throw new InvalidArgumentException('Usá una imagen publicitaria horizontal, con una proporción aproximada de 1920 × 720.');}
        $directory=$this->publicPath.'/'.trim($relativeDirectory,'/');
        if(!is_dir($directory)&&!mkdir($directory,0755,true)){throw new RuntimeException('No se pudo crear la carpeta de imágenes.');}
        $name=bin2hex(random_bytes(16)).'.'.$extension;
        if(!move_uploaded_file($tmp,$directory.'/'.$name)){throw new RuntimeException('No se pudo guardar la imagen.');}
        return trim($relativeDirectory,'/').'/'.$name;
    }
    private function deleteManagedFile(?string $path,string $relativeDirectory):void
    {
        $normalized=ltrim(str_replace('\\','/',trim((string)$path)),'/');
        $directory=trim($relativeDirectory,'/');
        $name=basename($normalized);
        if($normalized!==$directory.'/'.$name||!preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/',$name)){return;}
        $absolute=$this->publicPath.'/'.$directory.'/'.$name;
        if(is_file($absolute)&&!is_link($absolute)){@unlink($absolute);}
    }
}
