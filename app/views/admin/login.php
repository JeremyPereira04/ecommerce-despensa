<link rel="stylesheet" href="<?= e(asset('assets/css/admin-login.css')) ?>">

<div class="admin-auth-page">
    <span class="admin-auth-shape admin-auth-shape--gold" aria-hidden="true"></span>
    <div class="admin-auth-pattern" aria-hidden="true">
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--cart" focusable="false" viewBox="0 0 64 64"><path d="M7 11h8l6 29h27l7-20H19M26 52a4 4 0 1 0 0 .1M45 52a4 4 0 1 0 0 .1"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--produce" focusable="false" viewBox="0 0 64 64"><path d="M31 54C17 48 11 35 14 20c14 1 24 9 26 23M29 43c6-17 15-27 28-31 2 17-5 31-22 39M17 25c8 4 14 10 18 18M52 18c-8 9-13 19-17 33"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--bottle" focusable="false" viewBox="0 0 64 64"><path d="M25 8h14v10l5 7v27c0 3-2 5-5 5H25c-3 0-5-2-5-5V25l5-7V8ZM25 18h14M20 31h24"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--box" focusable="false" viewBox="0 0 64 64"><path d="m9 20 23-11 23 11-23 11L9 20Zm0 0v25l23 10 23-10V20M32 31v24"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--tag" focusable="false" viewBox="0 0 64 64"><path d="M10 14v18l24 24 22-22-24-24H14a4 4 0 0 0-4 4Z"/><circle cx="22" cy="22" r="3"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--chart" focusable="false" viewBox="0 0 64 64"><path d="M9 53V11M9 53h46M18 43l11-13 10 8 16-21"/><path d="M48 17h7v7"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--bag" focusable="false" viewBox="0 0 64 64"><path d="M12 21h40l-3 35H15l-3-35ZM23 24v-7a9 9 0 0 1 18 0v7"/></svg>
        <svg class="admin-auth-pattern__icon admin-auth-pattern__icon--barcode" focusable="false" viewBox="0 0 64 64"><path d="M10 14v36M16 14v36M23 14v36M28 14v36M36 14v36M43 14v36M48 14v36M54 14v36"/></svg>
    </div>
    <div class="admin-auth-lines" aria-hidden="true">
        <span class="admin-auth-line admin-auth-line--one"></span>
        <span class="admin-auth-line admin-auth-line--two"></span>
        <span class="admin-auth-line admin-auth-line--three"></span>
    </div>
    <section class="admin-auth-shell" aria-labelledby="admin-login-title">
        <aside class="admin-auth-brand" aria-labelledby="admin-brand-title">
            <div class="admin-auth-brand__decorations" aria-hidden="true">
                <svg class="admin-auth-brand__decoration admin-auth-brand__decoration--cart" viewBox="0 0 64 64"><path d="M8 12h7l5 28h27l7-19H18M25 51a3 3 0 1 0 0 .1M45 51a3 3 0 1 0 0 .1"/></svg>
                <svg class="admin-auth-brand__decoration admin-auth-brand__decoration--box" viewBox="0 0 64 64"><path d="m10 20 22-10 22 10-22 10-22-10Zm0 0v25l22 10 22-10V20M32 30v25"/></svg>
                <svg class="admin-auth-brand__decoration admin-auth-brand__decoration--tag" viewBox="0 0 64 64"><path d="M11 14v17l24 24 20-20-24-24H14a3 3 0 0 0-3 3Z"/><circle cx="22" cy="22" r="3"/></svg>
                <svg class="admin-auth-brand__decoration admin-auth-brand__decoration--chart" viewBox="0 0 64 64"><path d="M10 52V12M10 52h44M19 43l10-12 9 7 15-20"/></svg>
            </div>

            <img class="admin-auth-logo" src="<?= e(asset('assets/images/logo-despensa-para-todos-transparent.png')) ?>" width="2022" height="778" alt="Logo de Despensa Para Todos">
            <h2 class="admin-auth-brand__title" id="admin-brand-title">Gestioná tu negocio con <span class="admin-auth-brand__accent">claridad.</span></h2>
            <p class="admin-auth-brand__description">Controlá productos, pedidos, stock y clientes desde un solo lugar.</p>
            <picture class="admin-auth-brand__basket-picture" aria-hidden="true">
                <source srcset="<?= e(asset('assets/images/admin-login-basket-transparent.webp')) ?>" type="image/webp">
                <img class="admin-auth-basket" src="<?= e(asset('assets/images/admin-login-basket-transparent.png')) ?>" width="1297" height="1212" alt="" decoding="async" aria-hidden="true">
            </picture>
        </aside>

        <section class="admin-auth-form-panel" aria-labelledby="admin-login-title">
            <div class="admin-auth-form-wrapper">
                <header class="admin-auth-heading">
                    <p class="admin-auth-heading__eyebrow">Acceso administrativo</p>
                    <h1 id="admin-login-title">Bienvenido de nuevo</h1>
                    <p>Ingresá tus credenciales para continuar.</p>
                </header>

                <?php if (!empty($loginNotice)): ?>
                    <div class="admin-auth-alert" role="alert" aria-live="assertive"><?= e($loginNotice) ?></div>
                <?php endif; ?>

                <form class="admin-auth-form" action="<?= e(url('admin-login')) ?>" method="post" data-admin-login-form novalidate>
                    <?= csrf_input() ?>

                    <div class="admin-auth-field">
                        <label for="admin-email">Correo electrónico</label>
                        <span class="admin-auth-field__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
                        <input id="admin-email" name="email" type="email" inputmode="email" autocomplete="email" required maxlength="150" placeholder="nombre@ejemplo.com" value="<?= e($oldEmail ?? '') ?>" aria-describedby="admin-email-error">
                        <p class="admin-auth-field__error" id="admin-email-error" data-field-error="email" aria-live="polite"></p>
                    </div>

                    <div class="admin-auth-field">
                        <label for="admin-password">Contraseña</label>
                        <span class="admin-auth-field__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
                        <input id="admin-password" name="password" type="password" autocomplete="current-password" required maxlength="255" aria-describedby="admin-password-error">
                        <button class="admin-auth-password-toggle" type="button" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg></button>
                        <p class="admin-auth-field__error" id="admin-password-error" data-field-error="password" aria-live="polite"></p>
                    </div>

                    <button class="admin-auth-submit" type="submit" data-login-submit>
                        <span data-login-label>Iniciar sesión</span>
                        <span class="admin-auth-spinner" data-login-spinner aria-hidden="true" hidden></span>
                    </button>

                    <p class="admin-auth-security"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 3 20 6v5c0 5-3.4 8.7-8 10-4.6-1.3-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/></svg>Acceso exclusivo para personal autorizado</p>
                    <p class="visually-hidden" data-login-status aria-live="polite"></p>
                </form>

                <div class="admin-auth-navigation">
                    <a class="admin-auth-back" href="<?= e(url('home')) ?>">← Volver a la tienda</a>
                </div>
                <p class="admin-auth-copyright">© 2026 Despensa Para Todos</p>
            </div>
        </section>
    </section>
</div>
