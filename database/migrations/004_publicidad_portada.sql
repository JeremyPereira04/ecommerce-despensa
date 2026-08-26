BEGIN;

CREATE TABLE IF NOT EXISTS publicidad_portada (
    id_publicidad SMALLINT PRIMARY KEY DEFAULT 1,
    imagen VARCHAR(255),
    texto_alternativo VARCHAR(180) NOT NULL DEFAULT 'Promoción de Despensa Para Todos',
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_actualizacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_publicidad_portada_unica CHECK (id_publicidad = 1)
);

COMMIT;
