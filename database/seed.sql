BEGIN;

INSERT INTO categorias (nombre, descripcion) VALUES
('Gaseosas', 'Bebidas gaseosas de diferentes marcas y tamaños'),
('Aguas', 'Agua mineral con gas y sin gas'),
('Jugos', 'Jugos de diversos tamaños y marcas'),
('Limpieza', 'Productos de limpieza para el hogar'),
('Cárnicos', 'Carnes frescas, congeladas y embutidos'),
('Almacén', 'Arroz, fideos, harina, azúcar y otros');

INSERT INTO productos
(id_categoria,nombre,descripcion,codigo_barra,marca,presentacion,unidad_medida,precio,stock,imagen,activo,fecha_creacion,fecha_actualizacion)
VALUES
(1,'Coca-Cola 3L','Bebida gaseosa de 3 litros','1000100001','Coca-Cola','Botella 3L','L',10000,20,'/assets/images/products/coca-cola-3l.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(2,'Agua Dasani 2.25L','Agua mineral sin gas','1000100002','Dasani','Botella 2.25L','L',4500,30,'/assets/images/products/agua-dasani-2-25l.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(3,'Jugo Watts Durazno 1L','Néctar sabor durazno','1000100003','Watts','Caja 1L','L',14500,20,'/assets/images/products/jugo-watts-durazno-1l.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(4,'Escoba Puloil','Escoba doble barrida','1000100004','Puloil','Unidad','UNIDAD',38500,15,'/assets/images/products/escoba-puloil.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(5,'Costilla Tira Ancha','Costilla por kilogramo','1000100005',NULL,'Por kilogramo','KG',39950,10,'/assets/images/products/costilla-tira-ancha.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(6,'Arroz Primicia 500g','Arroz presentación 500 gramos','1000100006','Primicia','Bolsa 500g','G',6250,30,'/assets/images/products/arroz-primicia-500g.png',TRUE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);

INSERT INTO usuarios
(nombre,apellido,correo,contrasena_hash,telefono,direccion,rol,activo,fecha_creacion)
VALUES
('Jeremy','Pereira','jeremy@gmail.com','123456','0981123456','San Lorenzo','ADMIN',TRUE,CURRENT_TIMESTAMP),
('Fio','Sosa','fio_123@gmail.com','3422','0982234567','Asunción','CLIENTE',TRUE,CURRENT_TIMESTAMP);

INSERT INTO pedidos
(id_usuario,estado,direccion_entrega,total,observacion,fecha_creacion,fecha_actualizacion)
VALUES
(2,'PENDIENTE','Asunción',26250,'Pedido de prueba',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);

INSERT INTO detalles_pedido
(id_pedido,id_producto,cantidad,precio_unitario)
VALUES
(1,1,2,10000), (1,6,1,6250);

INSERT INTO pagos
( id_pedido, metodo, estado, monto, referencia_transaccion, fecha_creacion, fecha_confirmacion)
VALUES
(  1,  'EFECTIVO', 'PENDIENTE', 26250, 'TXN-000001', CURRENT_TIMESTAMP, NUL );

COMMIT;