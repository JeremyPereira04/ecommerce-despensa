BEGIN;

-- Fase 1: completa el modelo existente sin recrear tablas ni eliminar datos.
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS telefono VARCHAR(30),
    ADD COLUMN IF NOT EXISTS direccion TEXT;

ALTER TABLE productos
    ADD COLUMN IF NOT EXISTS codigo_barra VARCHAR(80),
    ADD COLUMN IF NOT EXISTS presentacion VARCHAR(100),
    ADD COLUMN IF NOT EXISTS unidad_medida VARCHAR(20);

CREATE UNIQUE INDEX IF NOT EXISTS productos_codigo_barra_unique
    ON productos (codigo_barra)
    WHERE codigo_barra IS NOT NULL;

CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido BIGSERIAL PRIMARY KEY,
    id_usuario BIGINT NOT NULL REFERENCES usuarios(id_usuario) ON DELETE RESTRICT,
    fecha_pedido TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
    total NUMERIC(12, 2) NOT NULL DEFAULT 0,
    CONSTRAINT pedidos_estado_valido
        CHECK (estado IN ('PENDIENTE', 'CONFIRMADO', 'PREPARANDO', 'ENTREGADO', 'CANCELADO')),
    CONSTRAINT pedidos_total_no_negativo CHECK (total >= 0)
);

CREATE TABLE IF NOT EXISTS detalles_pedido (
    id_detalle BIGSERIAL PRIMARY KEY,
    id_pedido BIGINT NOT NULL REFERENCES pedidos(id_pedido) ON DELETE CASCADE,
    id_producto BIGINT NOT NULL REFERENCES productos(id_producto) ON DELETE RESTRICT,
    cantidad INTEGER NOT NULL,
    precio_unitario NUMERIC(12, 2) NOT NULL,
    subtotal NUMERIC(12, 2) NOT NULL,
    CONSTRAINT detalles_pedido_cantidad_positiva CHECK (cantidad > 0),
    CONSTRAINT detalles_pedido_precio_no_negativo CHECK (precio_unitario >= 0),
    CONSTRAINT detalles_pedido_subtotal_no_negativo CHECK (subtotal >= 0),
    CONSTRAINT detalles_pedido_producto_unico UNIQUE (id_pedido, id_producto)
);

CREATE TABLE IF NOT EXISTS pagos (
    id_pago BIGSERIAL PRIMARY KEY,
    id_pedido BIGINT NOT NULL REFERENCES pedidos(id_pedido) ON DELETE RESTRICT,
    metodo VARCHAR(30) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
    monto NUMERIC(12, 2) NOT NULL,
    referencia_transaccion VARCHAR(150),
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_confirmacion TIMESTAMPTZ,
    CONSTRAINT pagos_estado_valido
        CHECK (estado IN ('PENDIENTE', 'APROBADO', 'RECHAZADO', 'CANCELADO', 'REEMBOLSADO')),
    CONSTRAINT pagos_monto_no_negativo CHECK (monto >= 0),
    CONSTRAINT pagos_confirmacion_consistente CHECK (
        estado <> 'APROBADO' OR fecha_confirmacion IS NOT NULL
    )
);

CREATE UNIQUE INDEX IF NOT EXISTS pagos_referencia_unique
    ON pagos (referencia_transaccion)
    WHERE referencia_transaccion IS NOT NULL;

CREATE INDEX IF NOT EXISTS pedidos_fecha_pedido_idx
    ON pedidos (fecha_pedido DESC);

CREATE INDEX IF NOT EXISTS pedidos_estado_idx
    ON pedidos (estado);

CREATE INDEX IF NOT EXISTS productos_activos_stock_idx
    ON productos (stock)
    WHERE activo = TRUE AND stock > 0;

CREATE INDEX IF NOT EXISTS pagos_estado_fecha_confirmacion_idx
    ON pagos (estado, fecha_confirmacion);

CREATE INDEX IF NOT EXISTS detalles_pedido_producto_idx
    ON detalles_pedido (id_producto);

COMMIT;
