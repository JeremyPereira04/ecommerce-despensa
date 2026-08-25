# Guía del frontend

## 1. Objetivo del rediseño

El rediseño convierte el esqueleto inicial en una experiencia pública navegable para una despensa: portada, categorías, catálogo, detalle de producto, carrito, acceso, registro, pedidos y checkout. La interfaz evita simular operaciones que todavía no tienen una implementación segura en el servidor.

El alcance funcional actual es:

- lectura de categorías y productos activos desde PostgreSQL;
- búsqueda, filtro por categoría y ordenamiento seguro mediante una lista permitida;
- detalle y productos relacionados;
- carrito en sesión con alta, actualización y eliminación;
- validación del producto, cantidad, stock y precio desde la base de datos en las operaciones del carrito;
- estados vacíos y de error cuando la base de datos no está disponible;
- autenticación de clientes, pedidos y checkout presentados como interfaces no operativas; el acceso administrativo sí posee autenticación de sesión propia.

### Identidad vigente: Despensa Para Todos

La marca visible del sitio es **Despensa Para Todos**. El header, los metadatos, el hero y el footer ya no contienen referencias a la identidad anterior.

El logo fuente se conserva en:

`public/assets/images/logo-despensa-para-todos.png`

Para el header y el footer oscuros se usa la variante:

`public/assets/images/logo-despensa-para-todos-transparent.png`

Esta variante mantiene el PNG transparente y reemplaza únicamente los elementos azul marino del logo por blanco para asegurar contraste sobre `#0B1F3A`, sin tarjeta, fondo blanco ni deformación. El layout declara sus dimensiones intrínsecas (2022×778), aplica `width: auto`, `height: auto` y límites máximos responsive. Se conservan `alt="Logo de Despensa Para Todos"`, el enlace al inicio, la comprobación de existencia y el fallback textual.

## 2. Tecnologías utilizadas

- PHP 8 con tipos estrictos, PDO y sesiones;
- PostgreSQL, respetando el esquema existente;
- HTML5 semántico;
- Bootstrap 5.3.3 como base de grilla, utilidades y navegación responsive;
- CSS personalizado con propiedades CSS;
- JavaScript nativo, cargado con `defer`;
- SVG local para el fallback de imágenes.

## 3. Estructura de componentes

- `app/views/layouts/header.php`: metadatos, estilos, enlace para saltar al contenido y apertura del layout.
- `app/views/layouts/navbar.php`: marca, buscador, navegación, cuenta y carrito.
- `app/views/layouts/footer.php`: beneficios globales, navegación secundaria, información del negocio, redes configuradas y scripts.
- `app/views/components/product-card.php`: tarjeta reutilizable con stock y acción de carrito.
- `app/views/components/alert.php`: mensajes de sesión escapados y descartables.
- `app/views/components/empty-state.php`: estado vacío reutilizable.
- `app/helpers/view.php`: escapado, URLs, assets, moneda, CSRF, flash y renderizado.
- `public/index.php`: punto de entrada y lista permitida de rutas públicas.
- `config/app.php`: datos públicos de contacto y URLs habilitadas de redes sociales.

Las vistas no ejecutan SQL. `Product` y `Category` concentran las consultas; los controladores preparan los datos de presentación.

## 4. Sistema visual

### Colores

- Azul marino principal `#0B1F3A`: header, títulos, precios, texto principal y footer.
- Azul marino oscuro `#07162A`: barra informativa y zonas de mayor profundidad.
- Amarillo dorado `#F5B642`: botones principales, foco, contador e indicadores.
- Turquesa `#20B8A6`: elementos complementarios, contornos y hover.
- Blanco `#FFFFFF`: superficies y contraste sobre azul.
- Fondo claro `#F5F7FA`: agrupación de contenido sin exceso de sombras.
- Texto secundario `#526071`: descripciones y metadatos.
- Éxito `#198754` y error `#DC3545`: estados semánticos.
- Estados de peligro y advertencia usan texto y contexto, no solamente color.

Todos los valores principales están centralizados en `:root` dentro de `styles.css`.

### Tipografía, contenedor y espaciados

Se usa la pila tipográfica del sistema para evitar una descarga adicional y mejorar el rendimiento. Los títulos tienen escala fluida con `clamp()`. El contenido principal y el footer comparten un contenedor centrado de `1200px`, con padding lateral de `24px` en escritorio y `20px` en móvil.

La escala base está centralizada en `:root`:

- `--space-1: 4px`
- `--space-2: 8px`
- `--space-3: 12px`
- `--space-4: 16px`
- `--space-5: 24px`
- `--space-6: 32px`
- `--space-7: 48px`
- `--space-8: 64px`

El footer usa texto base de `15px` con `line-height: 1.7`; sus títulos son de `14px`, peso 700, mayúsculas y color dorado. El copyright conserva un mínimo de `14px` para ser legible.

### Botones, formularios, tarjetas y alertas

- Botones con altura mínima táctil, estados hover, focus y disabled.
- Formularios con `label`, ayuda contextual y autocompletado apropiado.
- Tarjetas de producto uniformes mediante flexbox y relación de aspecto reservada.
- Alertas con `role="status"`, texto explícito y cierre accesible.
- Cantidades con botones reales y un campo numérico que conserva límites HTML.

### Estados

- Disponible y agotado se comunican con texto además del color.
- Los fallos de datos producen un mensaje genérico sin exponer excepciones.
- Catálogo, carrito, pedidos y rutas inexistentes tienen estados vacíos específicos.
- Login y registro de clientes, además del checkout, muestran el motivo por el cual permanecen deshabilitados. El login administrativo es un flujo separado y funcional.

## 5. Archivos creados

- `app/helpers/view.php`
- `app/views/home.php`
- `app/views/components/alert.php`
- `app/views/components/empty-state.php`
- `app/views/components/product-card.php`
- `app/views/errors/404.php`
- `public/assets/images/product-placeholder.svg`
- `public/assets/images/logo-despensa-para-todos.png`
- `public/assets/images/logo-despensa-para-todos-transparent.png`
- `config/app.php`
- `storage/sessions/.gitkeep`
- `docs/frontend-guide.md`

## 6. Archivos modificados

- `public/index.php`
- `app/controllers/ProductController.php`
- `app/controllers/CartController.php`
- `app/models/Product.php`
- `app/models/Category.php`
- `app/models/Cart.php`
- `app/views/layouts/header.php`
- `app/views/layouts/navbar.php`
- `app/views/layouts/footer.php`
- `app/views/products/index.php`
- `app/views/products/show.php`
- `app/views/categories/index.php`
- `app/views/cart/index.php`
- `app/views/auth/login.php`
- `app/views/auth/register.php`
- `app/views/orders/index.php`
- `app/views/orders/show.php`
- `app/views/orders/checkout.php`
- `app/views/orders/confirmation.php`
- `public/assets/css/styles.css`
- `public/assets/js/app.js`

La segunda mejora de identidad modificó únicamente:

- `app/views/layouts/header.php`
- `app/views/layouts/navbar.php`
- `app/views/home.php`
- `app/views/layouts/footer.php`
- `public/assets/css/styles.css`
- `docs/frontend-guide.md`

La mejora de layout y footer modificó:

- `config/app.php`
- `public/index.php`
- `app/views/layouts/header.php`
- `app/views/layouts/navbar.php`
- `app/views/layouts/footer.php`
- `app/views/home.php`
- `public/assets/css/styles.css`
- `docs/frontend-guide.md`
- `public/assets/images/logo-despensa-para-todos-transparent.png`

No se modificaron `database/schema.sql`, `database/seed.sql`, `tests/test_connection.php`, las imágenes de productos ni la configuración real de conexión.

## 7. Explicación de los cambios

- El front controller usa una lista cerrada de rutas para impedir inclusiones arbitrarias de archivos.
- El catálogo usa los campos reales del esquema: nombre, marca, presentación, unidad, precio, stock, imagen y categoría.
- La búsqueda utiliza parámetros preparados y el ordenamiento se selecciona mediante `match`, porque los nombres de columnas no deben venir directamente del navegador.
- El carrito guarda únicamente ID y cantidad en sesión. El precio y stock se vuelven a consultar; no se aceptan importes enviados por el navegador.
- Los componentes compartidos eliminan duplicación y permiten agregar nuevas páginas sin copiar layout.
- El fallback SVG evita imágenes rotas y conserva dimensiones para reducir desplazamientos visuales.
- El panel administrativo no fue inventado: los archivos existentes no tenían lógica, autorización ni datos confiables.

## 8. Decisiones técnicas y visuales importantes

- Se conservó PHP sin framework y se agregó solamente la infraestructura mínima para poder navegar las vistas.
- No se agregaron paquetes ni un proceso de compilación.
- Bootstrap se carga desde jsDelivr con integridad SRI. Para producción puede alojarse localmente y endurecer aún más la CSP.
- La identidad evita el aspecto Bootstrap genérico mediante una paleta, proporciones, tarjetas, portada y tipografía propias.
- La navegación móvil utiliza el componente collapse de Bootstrap y mantiene búsqueda y acciones principales accesibles.
- La navegación principal usa azul marino para reforzar reconocimiento; el buscador blanco y el botón dorado mantienen una relación de contraste clara.
- El hero conserva una composición liviana de CSS y SVG inline, sin fotografías ni peticiones externas adicionales. El emblema geométrico reemplaza cualquier referencia visual a la marca anterior.
- Antes del footer se muestra una franja global de cuatro beneficios: Compra segura, Stock actualizado, Atención cercana y Retiro rápido. Usa cuatro columnas en escritorio, dos en tablet y una en móvil.
- La portada mantiene el bloque de ubicación y contacto antes de la franja de beneficios, respetando el orden visual solicitado.
- El footer usa CSS Grid con `minmax(240px, 1.4fr) repeat(4, minmax(140px, 1fr))` en escritorio para marca, Comprar, Ayuda, Contacto y Horarios. Reduce a tres, dos y una columna según el ancho disponible.
- Los recursos de Ayuda sin rutas reales se muestran como texto atenuado, sin enlaces ni etiquetas técnicas visibles.
- El teléfono usa `white-space: nowrap` para no separarse en dos líneas, incluso en móvil.
- La información pública de contacto está escapada en pantalla y utiliza protocolos específicos (`tel:`, `mailto:` y HTTPS). Los destinos externos se abren con `rel="noopener noreferrer"`.

### Configuración de contacto y redes

`config/app.php` es el único punto para editar teléfono, WhatsApp, correo, ubicación, mapa y redes sociales. El footer tiene una lista fija de redes permitidas e íconos SVG locales; solo renderiza aquellas cuya URL no esté vacía. No se generan `href="#"` ni destinos simulados.

Estado actual:

- WhatsApp: habilitado con `https://wa.me/595994265663`.
- Facebook: pendiente de URL; no se renderiza.
- Instagram: pendiente de URL; no se renderiza.
- TikTok: pendiente de URL; no se renderiza.

Para habilitar una red, colocar una URL HTTPS válida en su clave dentro de `social`; para deshabilitarla, dejar la cadena vacía.

### Información pública del footer

- Teléfono: `tel:+595994265663`.
- WhatsApp: `https://wa.me/595994265663`.
- Correo: `mailto:rolonyere@gmail.com`.
- Ubicación: enlace de búsqueda de Google Maps para `MG37+89G, San Lorenzo 111428`, sin API ni claves.
- Horarios: lunes a viernes de 07:00 a 00:00, sábado de 12:00 a 19:00 y domingo cerrado.

## 9. Medidas de seguridad implementadas

- `htmlspecialchars()` con `ENT_QUOTES | ENT_SUBSTITUTE` y UTF-8 en el helper `e()`.
- Consultas preparadas para búsqueda, filtros, IDs y límites.
- Lista permitida para rutas y opciones de ordenamiento.
- Validación de IDs y cantidades con filtros enteros.
- Token CSRF con `random_bytes()` y comparación `hash_equals()` en acciones de carrito.
- Cookies de sesión `HttpOnly`, `SameSite=Lax` y `Secure` bajo HTTPS.
- Sesiones guardadas dentro de `storage/sessions`, evitando depender del directorio temporal global de XAMPP.
- La variable opcional `ECOMMERCE_SESSION_PATH` permite usar otro directorio de sesión en entornos aislados de prueba.
- Encabezados CSP, `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options` y `Permissions-Policy`.
- Errores externos genéricos; no se muestran excepciones, SQL, rutas ni credenciales.
- Reconsulta de producto, stock y precio en el servidor para el carrito.
- Formularios de autenticación de clientes deshabilitados hasta que exista lógica segura real; el acceso ADMIN se procesa por separado.

## 10. Riesgos de seguridad pendientes en el backend

- Implementar login con `password_hash()`, `password_verify()`, mensajes no enumerables, limitación de intentos y regeneración de sesión.
- El `seed.sql` actual contiene valores de contraseña que no parecen hashes seguros. Deben reemplazarse antes de usar esas cuentas.
- Implementar autorización por rol en todas las rutas administrativas; ocultar enlaces no es autorización.
- Validar propiedad de cada pedido para prevenir IDOR.
- Confirmar pedidos dentro de una transacción que bloquee o revalide stock, recalcule subtotales y total, y gestione concurrencia.
- Integrar pagos sin guardar secretos ni confiar en estados enviados por el navegador.
- Validar archivos subidos por MIME real, extensión, tamaño y nombre generado; almacenarlos fuera de rutas ejecutables.
- Agregar límites de frecuencia, registro seguro de eventos y una política de expiración de sesión.
- Revisar el SQL de seed: la inserción de pagos referencia `fecha_pago`, columna que no existe en el esquema mostrado.

## 11. Ejecutar y revisar

1. Configurar `config/database.php` localmente; el archivo está ignorado por Git.
2. Iniciar Apache y PostgreSQL desde el entorno local.
3. Abrir `http://localhost/Ecommerce-despensa/public/`.
4. Verificar portada, búsqueda, filtros, detalle y carrito.
5. Probar con la conexión detenida: la interfaz debe mostrar estados controlados, no trazas.

Validación de sintaxis en Windows/XAMPP:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { C:\xampp\php\php.exe -l $_.FullName }
```

Servidor de desarrollo alternativo:

```powershell
C:\xampp\php\php.exe -S localhost:8080 -t public
```

## 12. Agregar una nueva página sin duplicar código

1. Crear la vista dentro de `app/views`.
2. Preparar sus datos en un controlador o closure pequeño del front controller; no consultar SQL en la vista.
3. Añadir una entrada explícita en `$getRoutes` o `$postRoutes` de `public/index.php`.
4. Llamar `render('ruta/vista.php', ['pageTitle' => '...'])`.
5. Reutilizar los layouts y parciales; no incluir header y footer desde cada página.
6. Escapar cada valor dinámico con `e()` y usar `url()`/`asset()` para enlaces.

## 13. Reutilizar componentes

- Antes de incluir `product-card.php`, asignar un array `$product` con los campos consultados por `Product`.
- Para `empty-state.php`, definir `$emptyTitle`, `$emptyText`, `$emptyActionUrl` y `$emptyActionLabel`.
- Para mensajes tras un POST, usar `flash('success|danger|warning|info', 'Mensaje')` y redirigir.
- Incluir `csrf_input()` en cada formulario que cambie estado y validar con `verify_csrf()` antes de procesar.

## 14. Pruebas realizadas

- lint de todos los archivos PHP con el ejecutable de XAMPP;
- comprobación de rutas permitidas y enlaces generados;
- revisión del diff para excluir cambios de base de datos y archivos previos del usuario;
- revisión visual en 360, 768, 1024 y 1440 px mediante breakpoints;
- comprobación de dimensiones reservadas, `loading="lazy"`, fallback de imagen y scripts con `defer`;
- revisión de navegación por teclado, foco visible, labels y texto alternativo.
- revisión del header, logo/fallback, buscador, hero y footer en 360, 768, 1024 y 1440 px, comprobando ausencia de desbordamiento horizontal;
- comprobación de `alt`, dimensiones reservadas y proporción CSS del logo preparado;
- comprobación de los protocolos y atributos seguros de los enlaces de contacto externos;
- comprobación de que solo se renderizan redes con URL activa, sin `href="#"`;
- comprobación de que el teléfono permanece en una sola línea y no existe desbordamiento horizontal;
- comprobación de las grillas de beneficios y footer, del logo transparente y de la ausencia de tarjeta blanca;
- búsqueda global para confirmar que no quedan referencias visibles a “Despensa Central” o “Fresco”.
- lint de los 58 archivos PHP y validación sintáctica de `public/assets/js/admin.js`;
- prueba SQLite de ADMIN activo, contraseña incorrecta, correo inexistente, rol CLIENTE, ADMIN inactivo, CSRF, regeneración, expiración y cierre de sesión;
- acceso manual a `admin-dashboard` sin sesión, con redirección al login y sin contenido administrativo;
- POST de login sin CSRF, rechazado mediante redirección 303 y mensaje controlado;
- validación de campos vacíos, formato de correo, foco inicial del error y botón para mostrar/ocultar contraseña;
- revisión del orden lógico de controles interactivos y reglas `:focus-visible`;
- revisión responsive del login en 360×800, 768×1024, 1366×768, 1536×1024 y 1920×1080, sin scroll horizontal;
- confirmación de tarjeta 1240×720 px en 1536×1024, panel móvil de 140 px, un solo `h1`, recursos gráficos cargados y consola sin errores.

## 15. Problemas conocidos y tareas pendientes

- Login y registro de clientes, recuperación de contraseña, perfil, pedidos y checkout necesitan controladores y modelos seguros.
- El contenido del panel administrativo todavía usa datos de demostración; únicamente el login, la sesión, el control de rol y el cierre de sesión son reales.
- El catálogo limita la consulta a 60 elementos; falta paginación de servidor cuando el volumen lo requiera.
- Bootstrap depende actualmente del CDN y de acceso a Internet.
- Las pruebas end-to-end con una base cargada dependen del entorno PostgreSQL local.
- Debe corregirse y validarse el seed antes de recrear la base de datos.
- Preguntas frecuentes, cómo comprar, formas de pago, cambios y devoluciones y términos y condiciones todavía no tienen rutas propias; se muestran como texto atenuado sin enlaces.
- Facebook, Instagram y TikTok necesitan sus URLs públicas en `config/app.php`; mientras estén vacías no aparecen en el footer.

## 16. Panel administrativo y autenticación

El acceso se encuentra en `public/index.php?page=admin-login` y redirige a `public/index.php?page=admin-dashboard` después de autenticar un usuario activo con rol `ADMIN`. La pantalla usa el layout administrativo en modo login, por lo que no muestra header, buscador, carrito, footer público, sidebar ni topbar.

- Los datos provienen exclusivamente de `app/data/admin-mock.php`.
- Las rutas de contenido permanecen en la lista permitida y llaman a `require_admin()` antes de renderizar.
- `POST admin-login` valida CSRF, correo, campos obligatorios, estado activo, rol ADMIN y contraseña mediante `password_verify()`.
- La consulta del usuario está encapsulada en `User::findByEmail()` y usa PDO preparado.
- Tras el acceso se ejecuta `session_regenerate_id(true)` y la sesión conserva ID, nombre, correo, rol, estado autorizado y última actividad.
- La sesión administrativa expira después de 30 minutos de inactividad y se rechaza si ya no contiene el estado activo o el rol esperado.
- `POST admin-logout` exige sesión ADMIN y CSRF, elimina los datos, la cookie y la sesión.
- Hay un límite por sesión de cinco intentos fallidos dentro de quince minutos y mensajes externos genéricos.
- Las sesiones usan modo estricto, solamente cookies, `HttpOnly`, `SameSite=Lax` y `Secure` cuando la solicitud es HTTPS.
- Los formularios CRUD del prototipo se interceptan en el navegador y no persisten información.
- La vista previa de imágenes usa `FileReader` local y no carga archivos al servidor.
- Los estilos del login están aislados en `public/assets/css/admin-login.css`; los estilos generales del panel permanecen en `admin.css` y el comportamiento en `admin.js`.
- La prueba `tests/test_admin_auth.php` usa SQLite en memoria para verificar contraseña incorrecta, correo inexistente, rol CLIENTE, ADMIN inactivo, ADMIN válido, CSRF, regeneración y expiración de sesión, sin tocar PostgreSQL.
- La cuenta ADMIN incluida actualmente en `database/seed.sql` tiene una contraseña en texto plano y será rechazada. Debe reemplazarse por el resultado de `password_hash()` antes de intentar un acceso real.
- La recuperación de contraseña y la persistencia real del CRUD siguen pendientes; no se publican rutas falsas.

### Estructura visual del login

La pantalla se construye con HTML semántico, Bootstrap 5 y CSS, sin utilizar una captura completa como fondo:

- fondo exterior `#17191D`;
- tarjeta blanca centrada de hasta `1240px`, padding de `14px`, gap de `12px`, radio de `22px` y altura preferida de `720px`;
- escritorio desde 1200 px: panel institucional y formulario en proporción `3fr/2fr`;
- tablet de 768 a 1199 px: proporción aproximada `55%/45%`, padding exterior de `24px`;
- móvil por debajo de 768 px: una columna, padding de tarjeta de `8px`, panel azul de `140px` mostrando únicamente el logo;
- formulario de hasta `390px`, campos y botón de `52px`, controles táctiles de al menos `44px`;
- en 360 px se usan `12px` de padding exterior y `24px 18px` dentro del formulario;
- cuando la altura disponible es reducida, el documento permite desplazamiento vertical sin escalar ni recortar el contenido.

La paleta se centraliza en variables `--admin-*`: azul marino `#0B1F3A`, carbón `#17191D`, dorado `#F5B642`, turquesa `#20B8A6`, superficie `#F5F7FA` y borde `#D9E1EA`.

### Recursos gráficos

- Logo transparente: `public/assets/images/logo-despensa-para-todos-transparent.png`.
- Canasto PNG con alfa real: `public/assets/images/admin-login-basket-transparent.png`.
- Canasto WebP optimizado usado por navegadores compatibles: `public/assets/images/admin-login-basket-transparent.webp`.
- La pieza vertical `admin-login-brand-panel.*` queda como recurso histórico y ya no se carga en el login.

El canasto es decorativo, tiene `alt=""` y `aria-hidden="true"`. Las cuatro decoraciones lineales son SVG semánticamente ocultos con opacidad de 0.08. El logo conserva texto alternativo y dimensiones intrínsecas.

### Formulario y accesibilidad

- Existe un solo `h1`; el título institucional es `h2`.
- Cada input posee `label`, autocompletado, descripción de error y `aria-invalid` dinámico.
- Los errores de campos vacíos o correo inválido se anuncian con `aria-live`; el mensaje de servidor usa `role="alert"`.
- El botón de contraseña mantiene 44×44 px, cambia `aria-label` y no envía el formulario.
- El botón de acceso impide envíos repetidos y anuncia el estado de verificación.
- El foco de inputs, botón y enlaces es visible; las transiciones respetan `prefers-reduced-motion`.
- “Recordarme” se omitió porque el backend no posee tokens persistentes seguros.
- “¿Olvidaste tu contraseña?” se omitió porque no existe una ruta real de recuperación.

### Archivos del rediseño

Creados:

- `public/assets/images/admin-login-basket-transparent.png`
- `public/assets/images/admin-login-basket-transparent.webp`

Modificados:

- `app/views/admin/login.php`
- `app/views/layouts/admin-footer.php`
- `public/assets/css/admin-login.css`
- `public/assets/js/admin.js`
- `public/index.php`
- `app/helpers/auth.php`
- `tests/test_admin_auth.php`
- `docs/frontend-guide.md`

No se modificaron `database/schema.sql`, `database/seed.sql`, la conexión PostgreSQL ni el diseño público.

### Riesgos y tareas pendientes

- La cuenta ADMIN de `seed.sql` debe migrarse a un hash generado por `password_hash()`; no se cambió durante esta tarea.
- No existe recuperación de contraseña ni un “Recordarme” persistente seguro.
- La autorización activa se confirma al iniciar sesión y se conserva como estado mínimo de sesión; si se necesita revocación inmediata tras desactivar una cuenta, deberá revalidarse el usuario contra PostgreSQL en cada solicitud administrativa.
- El contenido del dashboard y sus CRUD continúa siendo un prototipo visual con datos de muestra.

### Fondo exterior decorativo

El fondo del login administrativo se amplió sin alterar la tarjeta, sus dimensiones, los paneles ni el formulario. Las capas, ordenadas detrás de `.admin-auth-shell`, son:

1. degradado lineal azul marino y carbón, sin negro puro;
2. tres resplandores radiales turquesa y dorado;
3. patrón de puntos blancos con opacidad 0.18;
4. forma orgánica turquesa inferior izquierda;
5. forma elíptica dorada superior derecha;
6. ocho iconos SVG lineales;
7. tres líneas circulares finas;
8. sombra doble de la tarjeta con profundidad negra y resplandor turquesa.

Los iconos reutilizan el sistema SVG inline local: carrito, vegetales, botella, caja, etiqueta, gráfico, bolsa y código de barras. No se agregó una biblioteca, imagen de fondo, base64, fotografía, JavaScript ni dependencia externa.

Desde 1200 px se muestran los ocho iconos y ambas formas completas. Entre 768 y 1199 px se muestran cinco iconos, baja la opacidad del patrón y se reducen las formas. Por debajo de 768 px permanecen solamente vegetales, botella y etiqueta; el patrón y las formas también se atenúan. La tarjeta conserva su comportamiento responsive previo y no cambia de tamaño por estas decoraciones.

Todas las capas poseen `aria-hidden="true"` cuando corresponden a HTML, `pointer-events: none` y `user-select: none`. No ingresan al orden de tabulación, no contienen texto, no usan animaciones continuas y permanecen por debajo de la tarjeta mediante aislamiento y niveles de apilamiento.

Archivos modificados por este ajuste:

- `public/assets/css/admin-login.css`
- `app/views/admin/login.php`
- `docs/frontend-guide.md`

El fondo se verificó en 360×800, 768×1024, 1366×768, 1536×1024 y 1920×1080. La tarjeta mantuvo las dimensiones previas, el número de iconos visibles fue 3/5/8 según el breakpoint y no se detectaron desbordamiento horizontal, elementos decorativos enfocables ni errores de consola.
