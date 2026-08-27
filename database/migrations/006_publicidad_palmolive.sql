BEGIN;

INSERT INTO publicidad_portada(imagen,texto_alternativo,orden,activo)
SELECT
    'assets/images/advertising/5b0e7f3280e89b4929aaab8548458ac2.jpg',
    'Nuevo lanzamiento Palmolive Naturals con oliva y aloe vera',
    30,
    TRUE
WHERE NOT EXISTS (
    SELECT 1
    FROM publicidad_portada
    WHERE imagen = 'assets/images/advertising/5b0e7f3280e89b4929aaab8548458ac2.jpg'
);

COMMIT;
