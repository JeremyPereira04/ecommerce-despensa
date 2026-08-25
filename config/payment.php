<?php
declare(strict_types=1);
$local=__DIR__.'/payment.local.php';
return is_file($local)?require $local:[
 'bank'=>'Configurar banco','holder'=>'Configurar titular','alias'=>'Configurar alias','account'=>'Configurar cuenta'
];
