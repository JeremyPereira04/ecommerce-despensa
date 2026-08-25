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
```

En producción, usá HTTPS, credenciales distintas por ambiente, backups automáticos y un usuario PostgreSQL con permisos mínimos. Conservá `storage/sessions` fuera de almacenamiento compartido o reemplazalo por Redis cuando haya más de una instancia web.
