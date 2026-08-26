# Ecommerce “Despensa Para Todos”

Aplicación PHP 8.2+ con PostgreSQL. El directorio público del servidor web debe ser `public/`, no la raíz del proyecto.

## Configuración local

1. Copiá `.env.example` fuera de cualquier commit y cargá sus valores como variables de entorno.
2. Creá la base PostgreSQL y ejecutá `database/schema.postgresql.sql`.
3. Creá un usuario con `rol = 'ADMIN'`, correo en minúsculas y un valor temporal en `contrasena_hash`.
4. Definí `ADMIN_EMAIL` y `ADMIN_PASSWORD`, y ejecutá:

   ```powershell
   php scripts/set_admin_password.php
   ```

   El script genera el hash con `password_hash(PASSWORD_DEFAULT)`. La contraseña en texto plano nunca debe guardarse en SQL, PHP ni Git.

5. Iniciá la aplicación desde esta carpeta:

   ```powershell
   php -S 127.0.0.1:8080 -t public
   ```

## Verificación

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
php tests/test_admin_auth.php
php tests/test_dashboard.php
php tests/test_category_images.php
```

## Imágenes de categorías

En instalaciones existentes, ejecutá `database/migrations/003_categoria_imagen.sql` antes de usar el formulario administrativo. La migración agrega `categorias.imagen VARCHAR(255)` de forma idempotente. Los archivos se validan en PHP y se guardan bajo `public/assets/images/categories/`; PostgreSQL conserva solamente la ruta relativa.

## Publicidad de portada

Ejecutá `database/migrations/004_publicidad_portada.sql` en instalaciones existentes. El administrador puede cargar desde **Configuración** un banner horizontal (idealmente 1920 × 720 px) que se guarda bajo `public/assets/images/advertising/`; la tabla `publicidad_portada` conserva solamente su ruta relativa, descripción accesible y estado.

## API administrativa

- `GET /api/admin/dashboard/stats`
- `GET /api/admin/orders/recent?limit=10` (`limit` entre 1 y 50)

Ambos endpoints requieren una sesión de administrador activo. Aplicá primero
`database/migrations/001_completar_modelo_comercial.sql`. En desarrollo el rate
limit usa archivos bajo `storage/rate-limits`; en producción con varias instancias
debe reemplazarse por Redis u otro almacenamiento compartido.

En producción, usá HTTPS, credenciales distintas por ambiente, backups automáticos y un usuario PostgreSQL con permisos mínimos. Conservá `storage/sessions` fuera de almacenamiento compartido o reemplazalo por Redis cuando haya más de una instancia web.
