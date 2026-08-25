<section class="hero-section">
    <div class="container">
        <div class="hero-card">
            <div class="hero-content">
                <span class="section-kicker">Variedad para todos los días</span>
                <h1>Todo lo que necesitás,<br><em>cerca de vos.</em></h1>
                <p>Encontrá bebidas, alimentos, productos de limpieza y mucho más en un solo lugar.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary btn-lg" href="<?= e(url('products')) ?>">Ver productos</a>
                    <a class="btn btn-outline-primary btn-lg" href="<?= e(url('categories')) ?>">Explorar categorías</a>
                </div>
                <ul class="hero-trust" aria-label="Beneficios">
                    <li><span aria-hidden="true">✓</span> Stock visible</li>
                    <li><span aria-hidden="true">✓</span> Precios claros</li>
                    <li><span aria-hidden="true">✓</span> Variedad para todos</li>
                </ul>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <span class="hero-orbit hero-orbit--one"></span>
                <span class="hero-orbit hero-orbit--two"></span>
                <div class="hero-emblem">
                    <svg viewBox="0 0 120 120"><path d="M24 44h72l-7 52H31l-7-52Z"/><path d="M43 46V34a17 17 0 0 1 34 0v12"/><path d="M60 57v25M47.5 69.5h25"/></svg>
                    <strong>Para todos</strong>
                    <small>Todos los días</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section category-section" aria-labelledby="category-title">
    <div class="container">
        <div class="section-heading">
            <div><span class="section-kicker">Encontrá rápido</span><h2 id="category-title">Comprá por categoría</h2></div>
            <a href="<?= e(url('categories')) ?>">Ver todas <span aria-hidden="true">→</span></a>
        </div>
        <?php if (!empty($categories)): ?>
            <div class="category-grid">
                <?php foreach (array_slice($categories, 0, 6) as $index => $category): ?>
                    <a class="category-tile category-tile--<?= ($index % 3) + 1 ?>" href="<?= e(url('products', ['category' => (int) $category['id_categoria']])) ?>">
                        <span class="category-tile__number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span><strong><?= e($category['nombre']) ?></strong><small><?= (int) $category['productos_count'] ?> productos</small></span>
                        <span class="category-tile__arrow" aria-hidden="true">↗</span>
                    </a>
                <?php endforeach; ?>
            </div>
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
