<?php $currentPage = (string) ($_GET['page'] ?? 'home'); ?>
<header class="site-header">
    <div class="utility-bar">
        <div class="container d-flex align-items-center justify-content-between gap-3">
            <span class="utility-message"><span class="utility-dot" aria-hidden="true"></span>Compras fáciles, precios claros y atención cercana.</span>
            <span class="utility-hours d-none d-md-inline"><svg class="utility-icon" aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/></svg>Lun–Vie 07:00–00:00 <span aria-hidden="true">·</span> Sáb 12:00–19:00</span>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg" aria-label="Navegación principal">
        <div class="container py-2 header-main">
            <a class="navbar-brand brand" href="<?= e(url('home')) ?>" aria-label="Despensa Para Todos, ir al inicio">
                <?php if ($brandLogoExists): ?>
                    <img class="brand__logo" src="<?= e(asset($brandLogoPath)) ?>" alt="Logo de Despensa Para Todos" width="2022" height="778">
                <?php else: ?>
                    <span class="brand__fallback"><strong>Despensa</strong><small>Para Todos</small></span>
                <?php endif; ?>
            </a>

            <div class="d-flex align-items-center gap-2 order-lg-3">
                <a class="header-action header-action--account d-none d-sm-flex" href="<?= e(url('login')) ?>">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm8 9a8 8 0 0 0-16 0"/></svg>
                    <span>Ingresar</span>
                </a>
                <a class="header-action header-action--cart" href="<?= e(url('cart')) ?>" aria-label="Carrito, <?= (int) $cartCount ?> productos">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L20.5 8H6M10 20h.01M18 20h.01"/></svg>
                    <span class="d-none d-sm-inline">Carrito</span>
                    <span class="cart-count"><?= (int) $cartCount ?></span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Abrir menú">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="mainNavigation">
                <form class="site-search" role="search" action="<?= e(url('products')) ?>" method="get">
                    <input type="hidden" name="page" value="products">
                    <label class="visually-hidden" for="header-search">Buscar productos</label>
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                    <input id="header-search" name="q" type="search" placeholder="¿Qué necesitás hoy?" value="<?= e($_GET['q'] ?? '') ?>">
                    <button type="submit">Buscar</button>
                </form>
                <ul class="navbar-nav nav-links">
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>" href="<?= e(url('home')) ?>">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'products' ? 'active' : '' ?>" href="<?= e(url('products')) ?>">Productos</a></li>
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'categories' ? 'active' : '' ?>" href="<?= e(url('categories')) ?>">Categorías</a></li>
                    <li class="nav-item d-sm-none"><a class="nav-link" href="<?= e(url('login')) ?>">Ingresar</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
