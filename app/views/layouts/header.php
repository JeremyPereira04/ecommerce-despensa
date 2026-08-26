<?php
$pageTitle = $pageTitle ?? 'Despensa Para Todos';
$pageDescription = $pageDescription ?? 'Tu compra cotidiana, simple y cerca.';
$bodyClass = $bodyClass ?? '';
$brandLogoPath = $bodyClass === 'page-home'
    ? 'assets/images/logo-despensa-para-todos.png'
    : 'assets/images/logo-despensa-para-todos-transparent.png';
$brandLogoExists = is_file(dirname(__DIR__, 3) . '/public/' . $brandLogoPath);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="theme-color" content="#0B1F3A">
    <title><?= e($pageTitle) ?> | Despensa Para Todos</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(versioned_asset('assets/css/styles.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main-content">Saltar al contenido</a>
<?php require __DIR__ . '/navbar.php'; ?>

<?php if (!empty($flashMessages)): ?>
    <div class="container alert-stack" aria-live="polite" aria-atomic="true">
        <?php foreach ($flashMessages as $message): ?>
            <?php require dirname(__DIR__) . '/components/alert.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<main id="main-content">
    <?php require $contentView; ?>
</main>

<?php require __DIR__ . '/footer.php'; ?>
