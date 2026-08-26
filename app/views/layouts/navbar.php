<?php
$currentPage = (string) ($_GET['page'] ?? 'home');
$customer = is_customer_authenticated() ? customer_user() : null;
$administrator = is_admin_authenticated() ? admin_user() : null;
$contactConfig = $GLOBALS['appConfig']['contact'] ?? [];
$deliveryArea = (string) ($contactConfig['delivery_area'] ?? 'Entrega en Asunción y Gran Asunción');
$deliveryMessage = (string) ($contactConfig['delivery_message'] ?? 'Envíos rápidos en el día');
$phoneDisplay = (string) ($contactConfig['phone_display'] ?? 'Contacto');
$phoneHref = (string) ($contactConfig['phone_href'] ?? '#');
$helpUrl = (string) ($contactConfig['help_url'] ?? '#');
$offersUrl = $currentPage === 'home' ? '#offers-banner' : url('home') . '#offers-banner';
?>
<header class="site-header">
    <div class="utility-bar">
        <div class="container utility-bar__content">
            <span><i class="bi bi-geo-alt" aria-hidden="true"></i><?= e($deliveryArea) ?></span>
            <span><i class="bi bi-truck" aria-hidden="true"></i><?= e($deliveryMessage) ?></span>
            <span class="utility-bar__links"><a href="<?= e($phoneHref) ?>"><i class="bi bi-telephone" aria-hidden="true"></i><?= e($phoneDisplay) ?></a><a href="<?= e($helpUrl) ?>" target="_blank" rel="noopener noreferrer">Ayuda</a></span>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg" aria-label="Navegación principal">
        <div class="container py-2 header-main">
            <button class="navbar-toggler order-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Abrir menú"><i class="bi bi-list" aria-hidden="true"></i></button>
            <a class="navbar-brand brand order-1 order-lg-0" href="<?= e(url('home')) ?>" aria-label="Despensa Para Todos, ir al inicio">
                <?php if ($brandLogoExists): ?>
                    <img class="brand__logo" src="<?= e(asset($brandLogoPath)) ?>" alt="Logo de Despensa Para Todos" width="2022" height="778">
                <?php else: ?>
                    <span class="brand__fallback"><strong>Despensa</strong><small>Para Todos</small></span>
                <?php endif; ?>
            </a>

            <div class="d-flex align-items-center gap-2 order-2 order-lg-3">
                <a class="header-action header-action--account" href="<?= e(url($customer ? 'orders' : 'login')) ?>" aria-label="<?= e($customer ? 'Cuenta de ' . $customer['name'] : 'Iniciar sesión') ?>">
                    <i class="bi bi-person" aria-hidden="true"></i><span class="d-none d-lg-inline"><?= e($customer ? $customer['name'] : 'Iniciar sesión') ?></span>
                </a>
                <a class="header-action header-action--cart" href="<?= e(url('cart')) ?>" aria-label="Carrito, <?= (int) $cartCount ?> productos">
                    <i class="bi bi-cart3" aria-hidden="true"></i><span class="d-none d-lg-inline">Carrito</span>
                    <span class="cart-count"><?= (int) $cartCount ?></span>
                </a>
            </div>

            <div class="collapse navbar-collapse order-3 order-lg-2" id="mainNavigation">
                <form class="site-search" role="search" action="<?= e(url('products')) ?>" method="get">
                    <input type="hidden" name="page" value="products">
                    <label class="visually-hidden" for="header-search">Buscar productos</label>
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="header-search" name="q" type="search" maxlength="120" placeholder="¿Qué necesitás hoy?" value="<?= e($_GET['q'] ?? '') ?>">
                    <button type="submit">Buscar</button>
                </form>
                <ul class="navbar-nav nav-links">
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="<?= e(url('home')) ?>">Inicio</a></li>
                    <li class="nav-item dropdown"><button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Categorías</button><ul class="dropdown-menu"><li><a class="dropdown-item" href="<?= e(url('categories')) ?>">Ver todas</a></li><?php foreach (array_slice($navigationCategories, 0, 10) as $navigationCategory): ?><li><a class="dropdown-item" href="<?= e(url('products', ['category' => (int) $navigationCategory['id_categoria']])) ?>"><?= e($navigationCategory['nombre']) ?></a></li><?php endforeach; ?></ul></li>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'products' ? 'active' : '' ?>" href="<?= e(url('products')) ?>">Productos</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e($offersUrl) ?>">Ofertas</a></li>
                    <?php if ($administrator): ?><li class="nav-item"><a class="nav-link" href="<?= e(url('admin-dashboard')) ?>">Administración</a></li><?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
