<?php
$advertisementAlt = trim((string)($advertisement['texto_alternativo'] ?? 'Promoción principal de Despensa Para Todos'));
$advertisementSource = advertisement_image($advertisement['imagen'] ?? null);
?>
<section class="home-ad-section" aria-label="Publicidad principal">
    <div class="home-ad-container">
        <div class="home-ad-banner">
            <img src="<?= e($advertisementSource) ?>" alt="<?= e($advertisementAlt) ?>" width="1920" height="720" data-image-fallback="<?= e(asset('assets/images/advertising/advertising-placeholder.svg')) ?>">
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

<section class="section section--soft" id="featured-products" aria-labelledby="featured-title">
    <div class="container">
        <div class="section-heading">
            <div><span class="section-kicker">Elegidos para vos</span><h2 id="featured-title">Productos destacados</h2></div>
            <a href="<?= e(url('products')) ?>">Ver catálogo <span aria-hidden="true">→</span></a>
        </div>
        <?php if (!empty($featuredProducts)): ?>
            <div class="row g-3 g-xl-4">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3"><?php require __DIR__ . '/components/product-card.php'; ?></div>
                <?php endforeach; ?>
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
