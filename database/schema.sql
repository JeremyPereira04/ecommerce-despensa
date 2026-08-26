CREATE DATABASE ecommerce_despensa;

CREATE TABLE usuarios (
    id_usuario INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(40),
    direccion VARCHAR(255),
    rol VARCHAR(20) NOT NULL DEFAULT 'CLIENTE',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_usuarios_rol
        CHECK (rol IN ('CLIENTE', 'ADMIN'))
);


CREATE TABLE categorias (
    id_categoria INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE publicidad_portada (
    id_publicidad SMALLINT PRIMARY KEY DEFAULT 1,
    imagen VARCHAR(255),
    texto_alternativo VARCHAR(180) NOT NULL DEFAULT 'Promoción de Despensa Para Todos',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_publicidad_portada_unica CHECK (id_publicidad = 1)
);


CREATE TABLE productos (
    id_producto INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_categoria INTEGER NOT NULL,
    codigo_barra VARCHAR(80) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    marca VARCHAR(100),
    presentacion VARCHAR(100),
    unidad_medida VARCHAR(50),
    precio NUMERIC(10, 2) NOT NULL,
    stock INTEGER NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_producto_precio
        CHECK (precio >= 0),

    CONSTRAINT chk_producto_stock
        CHECK (stock >= 0),

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id_categoria)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


CREATE TABLE pedidos (
    id_pedido INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,

    id_usuario INTEGER NOT NULL,

    estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',

    direccion_entrega VARCHAR(255) NOT NULL,

    total NUMERIC(12, 2) NOT NULL DEFAULT 0,

    observacion TEXT,

    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_pedidos_total
        CHECK (total >= 0),

    CONSTRAINT chk_pedidos_estado
        CHECK (
            estado IN (
                'PENDIENTE',
                'CONFIRMADO',
                'PREPARANDO',
                'ENTREGADO',
                'CANCELADO'
            )
        ),

    CONSTRAINT fk_pedidos_usuarios
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE detalles_pedido (
    id_detalle INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_pedido INTEGER NOT NULL,
    id_producto INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    precio_unitario NUMERIC(10, 2) NOT NULL,

    subtotal NUMERIC(12, 2)
        GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,

    CONSTRAINT chk_detalles_cantidad
        CHECK (cantidad > 0),

    CONSTRAINT chk_detalles_precio
        CHECK (precio_unitario >= 0),

    CONSTRAINT uq_detalles_pedido_producto
        UNIQUE (id_pedido, id_producto),

    CONSTRAINT fk_detalles_pedido
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detalles_producto
        FOREIGN KEY (id_producto)
        REFERENCES productos(id_producto)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


CREATE TABLE pagos (
    id_pago INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_pedido INTEGER NOT NULL,
    metodo VARCHAR(30) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
    monto NUMERIC(12, 2) NOT NULL,
    referencia_transaccion VARCHAR(150) UNIQUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_confirmacion TIMESTAMPTZ,

    CONSTRAINT chk_pagos_monto
        CHECK (monto > 0),

    CONSTRAINT chk_pagos_metodo
        CHECK (
            metodo IN (
                'EFECTIVO',
                'TARJETA',
                'TRANSFERENCIA'
            )
        ),

    CONSTRAINT chk_pagos_estado
        CHECK (
            estado IN (
                'PENDIENTE',
                'APROBADO',
                'RECHAZADO',
                'CANCELADO'
            )
        ),

    CONSTRAINT fk_pagos_pedidos
        FOREIGN KEY (id_pedido)
        REFERENCES pedidos(id_pedido)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
