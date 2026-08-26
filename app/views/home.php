<?php
$advertisementAlt = trim((string)($advertisement['texto_alternativo'] ?? 'Promoción principal de Despensa Para Todos'));
$advertisementSource = advertisement_image($advertisement['imagen'] ?? null);
?>
<section class="hero-section" aria-labelledby="home-hero-title">
    <div class="container">
        <div class="hero-card">
            <div class="hero-content">
                <span class="section-kicker">Variedad para todos los días</span>
                <h1 id="home-hero-title">Todo lo que necesitás, <em>cerca de vos.</em></h1>
                <p>Comprá fácil, recibí rápido.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary btn-lg" href="<?= e(url('products')) ?>">Ver productos</a>
                    <a class="btn btn-outline-primary btn-lg" href="#home-categories-title">Explorar categorías</a>
                </div>
                <ul class="hero-trust" aria-label="Beneficios del catálogo"><li><span aria-hidden="true">✓</span>Stock visible</li><li><span aria-hidden="true">✓</span>Precios claros</li><li><span aria-hidden="true">✓</span>Variedad para todos</li></ul>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <span class="hero-orbit hero-orbit--one"></span><span class="hero-orbit hero-orbit--two"></span>
                <div class="hero-product-collage">
                    <?php foreach (array_slice($featuredProducts ?? [], 0, 3) as $index => $heroProduct): ?>
                        <img class="hero-product hero-product--<?= $index + 1 ?>" src="<?= e(product_image($heroProduct['imagen'] ?? null)) ?>" alt="" width="360" height="360" data-image-fallback="<?= e(asset('assets/images/product-placeholder.svg')) ?>">
                    <?php endforeach; ?>
                    <span class="hero-shopping-mark"><i class="bi bi-basket2" aria-hidden="true"></i><strong>Para todos</strong><small>todos los días</small></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-category-strip" aria-label="Catálogo por categorías">
    <div class="container">
        <?php if (!empty($categories)): ?>
            <?php $categoryId=null;$categoryCarouselId='home-categories-title';$categoryCarouselTitle='Explorá nuestras categorías';$categoryCarouselKicker='Catálogo';require __DIR__.'/components/category-carousel.php'; ?>
        <?php else: ?>
            <p class="inline-empty"><?= e($dataError ?? 'Las categorías aparecerán cuando se conecte el catálogo.') ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section section--soft" id="featured-products" aria-labelledby="featured-title" data-horizontal-carousel>
    <div class="container">
        <div class="section-heading home-product-heading">
            <div><span class="section-kicker">Elegidos para vos</span><h2 id="featured-title">Productos destacados</h2></div>
            <div class="home-product-heading__actions"><a href="<?= e(url('products')) ?>">Ver catálogo <span aria-hidden="true">→</span></a><div class="carousel-controls"><button type="button" data-carousel-previous aria-label="Ver productos anteriores">‹</button><button type="button" data-carousel-next aria-label="Ver productos siguientes">›</button></div></div>
        </div>
        <?php if (!empty($featuredProducts)): ?>
            <div class="home-product-viewport" data-carousel-viewport tabindex="0" role="region" aria-label="Productos destacados">
              <div class="home-product-track">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="home-product-slide"><?php require __DIR__ . '/components/product-card.php'; ?></div>
                <?php endforeach; ?>
              </div>
            </div>
        <?php else: ?>
            <?php
            $emptyTitle = 'El catálogo está listo para recibir productos';
            $emptyText = $dataError ?? 'Cuando existan productos activos en la base de datos, se mostrarán aquí.';
            $emptyActionUrl = url('products');
            $emptyActionLabel = 'Abrir catálogo';
            require __DIR__ . '/components/empty-state.php';
            ?>
        <?php endif; ?>
    </div>
</section>

<section class="offers-section" id="offers-banner" aria-labelledby="offers-title">
    <div class="container">
        <div class="offers-banner">
            <div class="offers-banner__content"><span class="section-kicker">Comprá mejor</span><h2 id="offers-title">Ofertas que rinden más</h2><p>Ahorros para tu día a día</p><a class="btn btn-primary" href="<?= e(url('products')) ?>">Ver ofertas</a></div>
            <div class="offers-banner__media"><img src="<?= e($advertisementSource) ?>" alt="<?= e($advertisementAlt) ?>" width="1920" height="720" loading="lazy" data-image-fallback="<?= e(asset('assets/images/advertising/advertising-placeholder.svg')) ?>"></div>
        </div>
    </div>
</section>

<?php
$homeContact = ($GLOBALS['appConfig']['contact'] ?? []);
$homeLocation = (string) ($homeContact['location'] ?? 'MG37+89G, San Lorenzo 111428');
$homeMapsUrl = (string) ($homeContact['maps_url'] ?? 'https://www.google.com/maps/search/?api=1&query=MG37%2B89G%2C%20San%20Lorenzo%20111428');
?>
<section class="location-section" aria-labelledby="location-title">
    <div class="footer-container">
        <div class="location-card">
            <div class="location-card__content">
                <span class="section-kicker">Estamos cerca</span>
                <h2 id="location-title">Encontranos en San Lorenzo</h2>
                <p>Visitá nuestro local y retirá tu pedido de forma simple.</p>
                <address><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span><?= e($homeLocation) ?></span></address>
                <a class="btn btn-outline-primary" href="<?= e($homeMapsUrl) ?>" target="_blank" rel="noopener noreferrer">Abrir en Google Maps</a>
            </div>
            <a class="location-map" href="<?= e($homeMapsUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Abrir ubicación de Despensa Para Todos en Google Maps">
                <span class="location-map__pin" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                <span class="visually-hidden">Ver ubicación en Google Maps</span>
            </a>
        </div>
    </div>
</section>
