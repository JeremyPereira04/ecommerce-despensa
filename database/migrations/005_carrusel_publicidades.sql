BEGIN;

ALTER TABLE publicidad_portada DROP CONSTRAINT IF EXISTS chk_publicidad_portada_unica;
ALTER TABLE publicidad_portada ALTER COLUMN id_publicidad TYPE INTEGER;
ALTER TABLE publicidad_portada ADD COLUMN IF NOT EXISTS orden SMALLINT NOT NULL DEFAULT 0;
ALTER TABLE publicidad_portada ADD COLUMN IF NOT EXISTS fecha_creacion TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP;

DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'chk_publicidad_portada_orden') THEN
        ALTER TABLE publicidad_portada ADD CONSTRAINT chk_publicidad_portada_orden CHECK (orden BETWEEN 0 AND 999);
    END IF;
END $$;

DO $$
DECLARE
    identity_column BOOLEAN;
    next_identifier BIGINT;
BEGIN
    SELECT is_identity = 'YES' INTO identity_column
    FROM information_schema.columns
    WHERE table_schema = 'public' AND table_name = 'publicidad_portada' AND column_name = 'id_publicidad';

    IF NOT COALESCE(identity_column, FALSE) THEN
        CREATE SEQUENCE IF NOT EXISTS publicidad_portada_id_seq;
        ALTER SEQUENCE publicidad_portada_id_seq OWNED BY publicidad_portada.id_publicidad;
        ALTER TABLE publicidad_portada ALTER COLUMN id_publicidad SET DEFAULT nextval('publicidad_portada_id_seq');
        SELECT COALESCE(MAX(id_publicidad), 0) + 1 INTO next_identifier FROM publicidad_portada;
        PERFORM setval('publicidad_portada_id_seq', next_identifier, FALSE);
    END IF;
END $$;

INSERT INTO publicidad_portada(imagen,texto_alternativo,orden,activo)
SELECT 'assets/images/advertising/watts-kvseleccion-1920x650.jpg','Watts Selección, jugo de naranja cien por ciento fruta',10,TRUE
WHERE NOT EXISTS (SELECT 1 FROM publicidad_portada WHERE imagen='assets/images/advertising/watts-kvseleccion-1920x650.jpg');

INSERT INTO publicidad_portada(imagen,texto_alternativo,orden,activo)
SELECT 'assets/images/advertising/slider-retail-pechugon-1920x650.jpg','Pechugón, línea IQF en presentación familiar',20,TRUE
WHERE NOT EXISTS (SELECT 1 FROM publicidad_portada WHERE imagen='assets/images/advertising/slider-retail-pechugon-1920x650.jpg');

CREATE INDEX IF NOT EXISTS publicidad_portada_activo_orden_idx ON publicidad_portada (activo, orden, id_publicidad);

COMMIT;
