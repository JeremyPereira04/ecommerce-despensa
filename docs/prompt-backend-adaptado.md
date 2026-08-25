# Prompt para desarrollar el backend administrativo

Actúa como arquitecto backend senior especializado en PHP 8.2+, PostgreSQL, PDO y seguridad web.

Quiero que me guíes, con explicaciones didácticas, en la construcción progresiva del backend del panel administrativo de mi e-commerce **“Despensa Para Todos”**.

## Forma de trabajo obligatoria

- No generes todo el backend de una vez.
- Trabaja por fases pequeñas y espera mi confirmación antes de avanzar a la siguiente.
- Antes de proponer código, inspecciona los archivos reales del repositorio.
- Explícame qué problema resuelve cada cambio y por qué se implementa de esa manera.
- Indica exactamente qué archivo se crea o modifica.
- Conserva los nombres de tablas, columnas y conceptos del negocio en español.
- No reemplaces archivos completos si basta con un cambio localizado.
- No agregues frameworks ni dependencias externas sin explicar primero su necesidad y recibir mi autorización.
- Guíame para que yo escriba las partes principales; después revisa mi implementación.
- No hagas commits ni `push` sin que te lo solicite expresamente.

## Estado real del proyecto

El proyecto está dentro de `frontend-redesign-staging/` y actualmente utiliza:

- PHP 8.2 o superior, sin framework.
- PostgreSQL.
- PDO con consultas preparadas.
- Bootstrap 5 en el frontend.
- Sesiones PHP para autenticación administrativa.
- Apache/XAMPP previsto para desarrollo local.
- Un punto de entrada y enrutador sencillo en `public/index.php`.

La estructura existente es:

```text
frontend-redesign-staging/
├── app/
│   ├── controllers/
│   ├── data/
│   ├── helpers/
│   ├── models/
│   └── views/
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   └── schema.postgresql.sql
├── docs/
├── public/
│   └── index.php
├── scripts/
│   └── set_admin_password.php
├── storage/sessions/
└── tests/
```

Actualmente **no existen** los directorios `services`, `repositories`, `middlewares`, `validators` o `routes`. Tampoco existen `database/schema.sql` ni `database/seed.sql`. No asumas que existen: créalos únicamente cuando correspondan a una fase aprobada y explica su responsabilidad.

Las rutas actuales se declaran en los arrays `$getRoutes` y `$postRoutes` de `public/index.php`. Las vistas administrativas todavía consumen principalmente información simulada desde `app/data/admin-mock.php`.

No existe en el repositorio una imagen llamada `image_cd5763.jpg`; utiliza las vistas y estilos administrativos existentes como referencia visual.

## Archivos que deben auditarse primero

Antes de modificar código, revisa como mínimo:

- `database/schema.postgresql.sql`
- `config/database.php`
- `public/index.php`
- `app/helpers/auth.php`
- `app/helpers/view.php`
- `app/models/User.php`
- `app/models/Product.php`
- `app/models/Category.php`
- `app/controllers/AuthController.php`
- `app/data/admin-mock.php`
- `tests/test_admin_auth.php`

Comprueba también el estado de Git para no sobrescribir cambios existentes.

## Modelo de datos objetivo

No rediseñes las tablas desde cero. Audita el esquema real y añade únicamente lo necesario mediante migraciones incrementales.

### usuarios

- `id_usuario`
- `nombre`
- `apellido`
- `correo`
- `contrasena_hash`
- `telefono`
- `direccion`
- `rol`: `CLIENTE` o `ADMIN`
- `activo`
- `fecha_creacion`

Los administradores son usuarios con `rol = 'ADMIN'`; no deben guardarse en una tabla separada.

### categorias

- `id_categoria`
- `nombre`
- `descripcion`
- `activo`
- `fecha_creacion` si ya existe en el esquema

### productos

- `id_producto`
- `id_categoria`
- `nombre`
- `descripcion`
- `codigo_barra`
- `marca`
- `presentacion`
- `unidad_medida`
- `precio`
- `stock`
- `imagen`
- `activo`
- `fecha_creacion`
- `fecha_actualizacion`

### pedidos

- `id_pedido`
- `id_usuario`
- `fecha_pedido`
- `estado`: `PENDIENTE`, `CONFIRMADO`, `PREPARANDO`, `ENTREGADO` o `CANCELADO`
- `total`

### detalles_pedido

- `id_detalle`
- `id_pedido`
- `id_producto`
- `cantidad`
- `precio_unitario`
- `subtotal`

### pagos

- `id_pago`
- `id_pedido`
- `metodo`
- `estado`
- `monto`
- `referencia_transaccion`
- `fecha_creacion`
- `fecha_confirmacion`

Antes de agregar una restricción para `pagos.estado`, solicita o propone explícitamente la lista de estados permitidos. No la inventes silenciosamente.

## Objetivo funcional inicial

Implementar el backend del dashboard administrativo con estos endpoints JSON.

### Estadísticas

```http
GET /api/admin/dashboard/stats
```

Respuesta esperada:

```json
{
  "success": true,
  "data": {
    "ventas_hoy": 1250000,
    "pedidos_pendientes": 8,
    "productos_stock_bajo": 5
  }
}
```

Reglas:

- `ventas_hoy`: suma de pagos con estado `APROBADO` confirmados durante el día actual.
- `pedidos_pendientes`: cantidad de pedidos con estado `PENDIENTE`.
- `productos_stock_bajo`: productos activos con `stock > 0` y `stock <= limite`.
- El límite debe venir de configuración y usar `5` como valor predeterminado.
- Los importes deben ser números, sin texto ni `Gs.`.
- La zona horaria del negocio es `America/Asuncion`.
- La consulta diaria debe usar un intervalo `[inicio_del_día, inicio_del_día_siguiente)` para conservar precisión y aprovechar índices.

### Pedidos recientes

```http
GET /api/admin/orders/recent?limit=10
```

Debe devolver solamente:

- ID del pedido.
- Nombre completo del cliente.
- Fecha del pedido.
- Total numérico.
- Estado.

Reglas:

- Ordenar por `fecha_pedido` descendente.
- Límite predeterminado: `10`.
- Límite máximo: `50`.
- Convertir y validar explícitamente `limit`.
- No concatenar entradas del usuario directamente en SQL.
- No devolver hashes, contraseñas, teléfonos, direcciones ni columnas innecesarias.
- Permitir acceso únicamente a usuarios autenticados, activos y con rol `ADMIN`.

## Arquitectura incremental requerida

Cuando la fase correspondiente sea aprobada, introducir separación Controller–Service–Repository:

### DashboardRepository

- Recibe `PDO` mediante el constructor.
- Contiene exclusivamente consultas SQL y mapeo básico de resultados.
- Usa sentencias preparadas.
- No accede a `$_GET`, `$_POST`, `$_SESSION` ni genera JSON o HTML.

### DashboardService

- Recibe el repositorio mediante el constructor.
- Aplica reglas de negocio y combina resultados.
- No accede a variables globales.
- No genera respuestas HTTP, JSON o HTML.

### DashboardController

- Recibe el servicio mediante el constructor.
- Lee y valida los datos necesarios de la solicitud.
- Invoca el servicio.
- Define el código HTTP y devuelve JSON.
- No contiene SQL.

Aplica responsabilidad única e inversión de dependencias sin crear interfaces vacías mientras exista una sola implementación.

## Seguridad obligatoria

### Autenticación y autorización

Adapta la seguridad existente en `app/helpers/auth.php` sin duplicar innecesariamente su lógica.

Para endpoints JSON:

- Responder `401` cuando no existe una sesión autenticada.
- Responder `403` cuando existe sesión, pero el usuario no es administrador, está inactivo o perdió autorización.
- No redirigir a una vista HTML desde un endpoint API.
- Verificar el estado y rol actuales contra la base cuando el riesgo o la operación lo requieran; no confiar indefinidamente en datos antiguos de la sesión.
- Mantener la regeneración del ID de sesión después del login.

No reemplazar sesiones por JWT salvo que aparezca un frontend en otro origen o una necesidad verificable y se explique antes el cambio.

### Contraseñas

- Preferir `PASSWORD_ARGON2ID` cuando esté disponible.
- Usar `PASSWORD_DEFAULT` como alternativa.
- Verificar con `password_verify()`.
- Considerar `password_needs_rehash()` después de un login correcto.
- Nunca almacenar, registrar o devolver contraseñas en texto plano.
- Utilizar `scripts/set_admin_password.php` para actualizar el hash administrativo de forma controlada.

### CSRF

Para `POST`, `PUT`, `PATCH` y `DELETE`:

- Token generado con `random_bytes()`.
- Token almacenado en sesión.
- Comparación mediante `hash_equals()`.
- Respuesta HTTP `403` si falta o es inválido.

Los endpoints `GET` no deben modificar datos.

### Validación

- Validar tipo, longitud, formato, rango y valores permitidos.
- Usar listas blancas para roles y estados.
- Crear validadores específicos solamente cuando aparezcan reglas suficientes para justificar la carpeta `app/validators/`.
- No transformar datos normales con `htmlspecialchars()` antes de guardarlos.
- Escapar siempre al mostrar datos en HTML mediante el helper `e()` existente.

### SQL, JSON y errores

- Usar PDO preparado para todos los valores recibidos.
- Usar listas blancas para elementos estructurales que PDO no pueda parametrizar.
- Responder JSON con `Content-Type: application/json; charset=utf-8`.
- No mostrar excepciones, trazas, DSN ni credenciales al cliente.
- Registrar eventos relevantes sin contraseñas, tokens ni otros secretos.

### Sesiones, cabeceras y CORS

- Conservar `HttpOnly`, `Secure` cuando haya HTTPS y `SameSite=Lax` o `Strict`.
- Conservar las cabeceras de seguridad ya presentes en `public/index.php`.
- Añadir HSTS solamente en producción bajo HTTPS.
- No habilitar CORS si frontend y backend comparten origen.
- Nunca usar `Access-Control-Allow-Origin: *` con endpoints autenticados.

### Rate limiting

- Login: máximo 5 intentos por cuenta e IP durante 15 minutos.
- API administrativa: máximo 60 solicitudes por minuto por usuario autenticado.
- Para desarrollo se admite almacenamiento local controlado.
- Explicar que producción con múltiples instancias requiere Redis u otro almacenamiento compartido.
- No presentar el rate limiting de aplicación como protección completa contra DDoS.

## Rendimiento y consistencia

Antes de crear índices, comprueba cuáles ya existen en `schema.postgresql.sql` y, si hay conexión a PostgreSQL, consulta también el catálogo real.

Evalúa específicamente:

- `pedidos(fecha_pedido DESC)` para pedidos recientes.
- `pedidos(estado)` para pendientes.
- Un índice parcial de productos activos con stock mayor que cero para stock bajo.
- `pagos(estado, fecha_confirmacion)` para ventas diarias.

Explica qué consulta optimiza cada índice y evita índices duplicados o de bajo valor.

## Fases de implementación

### Fase 1: auditoría y migración propuesta

- Revisar el esquema real.
- Identificar tablas y columnas ausentes.
- Revisar restricciones, claves foráneas e índices.
- Proponer una migración incremental; no ejecutarla sin confirmación.
- No modificar todavía código PHP.

### Fase 2: SQL del dashboard

- Diseñar por separado las consultas de estadísticas y pedidos recientes.
- Explicar parámetros, tipos devueltos, zona horaria e índices utilizados.
- Probarlas si existe una base disponible.
- Esperar confirmación antes de crear clases PHP.

### Fase 3: arquitectura PHP

- Crear `app/repositories/DashboardRepository.php`.
- Crear `app/services/DashboardService.php`.
- Crear `app/controllers/DashboardController.php`.
- Integrar las rutas API con el enrutador real de `public/index.php` o proponer una extracción pequeña y justificada.
- Mostrar y explicar un archivo por vez.

### Fase 4: seguridad de la API

- Adaptar la autenticación existente a respuestas JSON.
- Añadir autorización administrativa.
- Añadir rate limiting.
- Comprobar CSRF para futuras operaciones de escritura.
- Revisar sesiones, cabeceras y manejo seguro de errores.

### Fase 5: pruebas

Crear pruebas o casos verificables para:

- Administrador autenticado.
- Cliente autenticado intentando entrar.
- Usuario no autenticado.
- Usuario administrador desactivado después de iniciar sesión.
- `limit` ausente, válido, no numérico, negativo, cero y mayor que `50`.
- Ausencia de ventas aprobadas.
- Producto sin stock, que no debe contar como stock bajo.
- Producto con stock exactamente igual al límite, que sí debe contar.
- Cambio de fecha en `America/Asuncion`.
- Error controlado de base de datos.
- Ausencia accidental de datos sensibles en el JSON.

## Formato requerido en cada respuesta

En cada fase incluye:

1. Objetivo de la fase.
2. Estado actual observado.
3. Archivos afectados.
4. Decisiones técnicas.
5. Código estrictamente necesario.
6. Explicación de seguridad.
7. Forma de probarlo.
8. Posibles errores o casos límite.
9. Una pregunta o ejercicio breve para comprobar que comprendí.
10. Una petición explícita de confirmación antes de avanzar.

Cuando yo escriba código, revísalo en cuanto a lógica, seguridad, nomenclatura, responsabilidad única, PHP idiomático, SQL, manejo de errores y posibles fallos en runtime. Señala primero los problemas importantes y luego propón correcciones pequeñas y justificadas.

Comienza únicamente con la **Fase 1: auditoría del esquema actual y propuesta de migración incremental**. No modifiques archivos durante esa primera respuesta.
