BEGIN;

CREATE TABLE IF NOT EXISTS categorias (
    id_categoria BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    imagen VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS productos (
    id_producto BIGSERIAL PRIMARY KEY,
    id_categoria BIGINT NOT NULL REFERENCES categorias(id_categoria),
    nombre VARCHAR(160) NOT NULL,
    descripcion TEXT,
    marca VARCHAR(100),
    precio NUMERIC(12, 2) NOT NULL CHECK (precio >= 0),
    stock INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0),
    imagen VARCHAR(500),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(254) NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'CLIENTE' CHECK (rol IN ('ADMIN', 'CLIENTE')),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT usuarios_correo_normalizado CHECK (correo = LOWER(TRIM(correo)))
);

CREATE TABLE IF NOT EXISTS publicidad_portada (
    id_publicidad SMALLINT PRIMARY KEY DEFAULT 1,
    imagen VARCHAR(255),
    texto_alternativo VARCHAR(180) NOT NULL DEFAULT 'Promoción de Despensa Para Todos',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_publicidad_portada_unica CHECK (id_publicidad = 1)
);

CREATE UNIQUE INDEX IF NOT EXISTS usuarios_correo_unique_ci ON usuarios (LOWER(correo));
CREATE INDEX IF NOT EXISTS productos_categoria_activo_idx ON productos (id_categoria, activo);
CREATE INDEX IF NOT EXISTS productos_activo_fecha_idx ON productos (activo, fecha_creacion DESC);

COMMIT;
