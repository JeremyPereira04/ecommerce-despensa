<?php
declare(strict_types=1);
return [
    'adminProducts' => [
        ['name'=>'Coca-Cola 3L','barcode'=>'1000100001','category'=>'Gaseosas','price'=>10000,'stock'=>20,'active'=>true,'image'=>'/assets/images/products/coca-cola-3l.png','brand'=>'Coca-Cola','presentation'=>'Botella 3L','unit'=>'L','description'=>'Bebida gaseosa de 3 litros.'],
        ['name'=>'Agua Dasani 2.25L','barcode'=>'1000100002','category'=>'Aguas','price'=>4500,'stock'=>30,'active'=>true,'image'=>'/assets/images/products/agua-dasani-2-25l.png'],
        ['name'=>'Jugo Watts Durazno 1L','barcode'=>'1000100003','category'=>'Jugos','price'=>14500,'stock'=>4,'active'=>true,'image'=>'/assets/images/products/jugo-watts-durazno-1l.png'],
        ['name'=>'Escoba Puloil','barcode'=>'1000100004','category'=>'Limpieza','price'=>38500,'stock'=>0,'active'=>false,'image'=>'/assets/images/products/escoba-puloil.png'],
    ],
    'adminCategories' => [['name'=>'Gaseosas','count'=>12],['name'=>'Aguas','count'=>8],['name'=>'Jugos','count'=>10],['name'=>'Limpieza','count'=>16],['name'=>'Cárnicos','count'=>7],['name'=>'Almacén','count'=>24]],
    'adminOrders' => [['id'=>'PED-1048','customer'=>'María López','date'=>'24/08/2026','total'=>126500,'status'=>'Pendiente'],['id'=>'PED-1047','customer'=>'Carlos Benítez','date'=>'24/08/2026','total'=>84000,'status'=>'Preparando'],['id'=>'PED-1046','customer'=>'Ana Duarte','date'=>'23/08/2026','total'=>215000,'status'=>'Entregado']],
];
