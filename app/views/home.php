<?php $advertisements=$advertisements??[]; ?>
<section class="hero-section" aria-labelledby="home-hero-title">
    <div class="container">
        <div class="hero-card">
            <div class="hero-content">
                <h1 id="home-hero-title">Todo lo que necesitás, <em>cerca de vos.</em></h1>
                <p>Comprá fácil, recibí rápido.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary btn-lg" href="<?= e(url('products')) ?>">Ver productos</a>
                    <a class="btn btn-outline-primary btn-lg" href="#home-categories-title">Explorar categorías</a>
                </div>
                <ul class="hero-trust" aria-label="Beneficios del catálogo"><li><span aria-hidden="true"><i class="bi bi-check-lg"></i></span>Stock visible</li><li><span aria-hidden="true"><i class="bi bi-tag"></i></span>Precios claros</li><li><span aria-hidden="true"><i class="bi bi-truck"></i></span>Variedad para todos</li></ul>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <span class="hero-orbit hero-orbit--one"></span><span class="hero-orbit hero-orbit--two"></span>
                <img class="hero-grocery-art" src="<?= e(asset('assets/images/hero-grocery-basket-final.webp')) ?>" alt="" width="1182" height="832" fetchpriority="high">
            </div>
        </div>
    </div>
</section>

<section class="home-category-strip" aria-label="Catálogo por categorías">
    <div class="container">
        <?php if (!empty($categories)): ?>
            <?php $categoryId=null;$categoryCarouselId='home-categories-title';$categoryCarouselTitle='Explorá nuestras categorías';$categoryCarouselKicker='Catálogo';$categoryCarouselCompact=true;require __DIR__.'/components/category-carousel.php'; ?>
        <?php else: ?>
            <p class="inline-empty"><?= e($dataError ?? 'Las categorías aparecerán cuando se conecte el catálogo.') ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="home-showcase" id="featured-products" aria-labelledby="featured-title">
    <div class="container home-showcase__grid">
      <div class="home-featured-panel" data-horizontal-carousel>
        <div class="section-heading home-product-heading">
            <div><span class="section-kicker">Elegidos para vos</span><h2 id="featured-title">Productos destacados</h2></div>
            <div class="home-product-heading__actions"><a href="<?= e(url('products')) ?>">Ver todos <span aria-hidden="true">›</span></a><div class="carousel-controls"><button type="button" data-carousel-previous aria-label="Ver productos anteriores">‹</button><button type="button" data-carousel-next aria-label="Ver productos siguientes">›</button></div></div>
        </div>
        <?php if (!empty($featuredProducts)): ?>
            <div class="home-product-viewport" data-carousel-viewport tabindex="0" role="region" aria-label="Productos destacados">
              <div class="home-product-track">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="home-product-slide"><?php $productCardCompact=true;require __DIR__ . '/components/product-card.php'; ?></div>
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
      <aside class="offers-section" id="offers-banner" aria-labelledby="offers-title">
        <div class="offers-banner">
            <div class="offers-banner__content"><h2 id="offers-title">Ofertas que<br>rinden más</h2><p>Ahorros para tu día a día</p><a class="btn btn-primary" href="<?= e(url('products')) ?>">Ver ofertas</a></div>
            <div class="offers-banner__media">
                <img class="offers-basket-art" src="<?=e(asset('assets/images/hero-grocery-basket-final.webp'))?>" alt="" width="1182" height="832" loading="lazy">
                <span class="offers-discount" aria-hidden="true"><i class="bi bi-percent"></i></span>
            </div>
        </div>
      </aside>
    </div>
</section>

<?php if($advertisements): ?>
<section class="home-ad-section" aria-label="Publicidades destacadas">
    <div class="home-ad-container">
        <div class="home-ad-carousel" data-advertisement-carousel data-interval="6000" aria-roledescription="carrusel">
            <div class="home-ad-slides">
                <?php foreach($advertisements as $index=>$item): ?>
                    <article class="home-ad-slide <?=$index===0?'is-active':''?>" data-advertisement-slide aria-hidden="<?=$index===0?'false':'true'?>"><img src="<?=e(advertisement_image($item['imagen']??null))?>" alt="<?=e($item['texto_alternativo']??'Promoción de Despensa Para Todos')?>" width="1920" height="650" loading="<?=$index===0?'eager':'lazy'?>" data-image-fallback="<?=e(asset('assets/images/advertising/advertising-placeholder.svg'))?>"></article>
                <?php endforeach; ?>
            </div>
            <?php if(count($advertisements)>1): ?>
                <button class="home-ad-control home-ad-control--previous" type="button" data-advertisement-previous aria-label="Publicidad anterior"><i class="bi bi-chevron-left"></i></button>
                <button class="home-ad-control home-ad-control--next" type="button" data-advertisement-next aria-label="Publicidad siguiente"><i class="bi bi-chevron-right"></i></button>
                <div class="home-ad-indicators" aria-label="Elegir publicidad"><?php foreach($advertisements as $index=>$item): ?><button class="<?=$index===0?'is-active':''?>" type="button" data-advertisement-indicator="<?=$index?>" aria-label="Ver publicidad <?=$index+1?>" aria-current="<?=$index===0?'true':'false'?>"></button><?php endforeach; ?></div>
            <?php endif; ?>
            <span class="visually-hidden" aria-live="polite" data-advertisement-status>Publicidad 1 de <?=count($advertisements)?></span>
        </div>
    </div>
</section>
<?php endif; ?>
