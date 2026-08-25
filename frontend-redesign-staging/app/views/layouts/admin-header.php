<?php
$pageTitle = $pageTitle ?? 'Administración';
$adminSection = $adminSection ?? '';
$adminLogin = $adminLogin ?? false;
$adminLogo = 'assets/images/logo-despensa-para-todos-transparent.png';
$currentAdmin = admin_user();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B1F3A">
    <title><?= e($pageTitle) ?> | Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= e(asset('assets/css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('assets/css/admin-auth-ui.css')) ?>">
</head>
<body class="admin-body <?= $adminLogin ? 'admin-body--login' : '' ?>">
<a class="skip-link" href="#admin-content">Saltar al contenido</a>
<?php if (!$adminLogin): ?>
    <div class="admin-shell">
        <?php require __DIR__ . '/admin-sidebar.php'; ?>
        <div class="admin-workspace">
            <header class="admin-topbar">
                <button class="admin-menu-button" type="button" data-admin-menu aria-controls="admin-sidebar" aria-expanded="false" aria-label="Abrir menú">☰</button>
                <div><span>Panel administrativo</span><h1><?= e($pageTitle) ?></h1></div>
                <div class="admin-profile"><b aria-hidden="true"><?= e(strtoupper(substr((string) ($currentAdmin['name'] ?? 'A'), 0, 1))) ?></b><span><strong><?= e($currentAdmin['name'] ?? 'Administrador') ?></strong><small><?= e($currentAdmin['email'] ?? '') ?></small></span></div>
            </header>
            <main id="admin-content" class="admin-content"><?php require $contentView; ?></main>
        </div>
    </div>
<?php else: ?>
    <main id="admin-content"><?php require $contentView; ?></main>
<?php endif; ?>
<?php require __DIR__ . '/admin-footer.php'; ?>
