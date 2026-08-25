<?php
declare(strict_types=1);

final class AdminCatalogService
{
    private const UNITS=['Unidad','L','ML','KG','G'];
    public function __construct(private readonly AdminCatalogRepository $repo){}
    public function productos():array{return $this->repo->productos();}
    public function producto(int $id):?array{return $this->repo->producto($id);}
    public function categorias():array{return $this->repo->categorias();}
    public function guardarProducto(array $input,?int $id,?string $image):int
    {
        $name=trim((string)($input['nombre']??''));$category=filter_var($input['id_categoria']??null,FILTER_VALIDATE_INT);$price=filter_var($input['precio']??null,FILTER_VALIDATE_FLOAT);$stock=filter_var($input['stock']??null,FILTER_VALIDATE_INT);$unit=(string)($input['unidad_medida']??'Unidad');
        if($name===''||mb_strlen($name)>160||$category===false||$category<1||$price===false||$price<0||$stock===false||$stock<0||!in_array($unit,self::UNITS,true)){throw new InvalidArgumentException('Revisá nombre, categoría, precio, stock y unidad de medida.');}
        $current=$id?$this->repo->producto($id):null;
        return $this->repo->guardarProducto(['id_categoria'=>$category,'nombre'=>$name,'descripcion'=>$this->nullable($input['descripcion']??null,2000),'codigo_barra'=>$this->nullable($input['codigo_barra']??null,80),'marca'=>$this->nullable($input['marca']??null,100),'presentacion'=>$this->nullable($input['presentacion']??null,100),'unidad_medida'=>$unit,'precio'=>$price,'stock'=>$stock,'imagen'=>$image??($current['imagen']??null),'activo'=>isset($input['activo'])?1:0],$id);
    }
    public function guardarCategoria(array $input,?int $id):void{$name=trim((string)($input['nombre']??''));if($name===''||mb_strlen($name)>100){throw new InvalidArgumentException('El nombre de categoría es obligatorio y admite hasta 100 caracteres.');}$this->repo->guardarCategoria(['nombre'=>$name,'descripcion'=>$this->nullable($input['descripcion']??null,1000)],$id);}
    public function cambiarProducto(int $id):void{$this->repo->cambiarProducto($id);}
    public function cambiarCategoria(int $id):void{$this->repo->cambiarCategoria($id);}
    private function nullable(mixed $v,int $max):?string{$v=trim((string)$v);if($v==='')return null;if(mb_strlen($v)>$max)throw new InvalidArgumentException('Uno de los campos supera la longitud permitida.');return $v;}
}
