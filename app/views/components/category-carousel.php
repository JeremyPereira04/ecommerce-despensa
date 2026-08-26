<?php
$categoryCarouselTitle = $categoryCarouselTitle ?? 'Comprá por categoría';
$categoryCarouselKicker = $categoryCarouselKicker ?? 'Encontrá rápido';
$categoryCarouselId = $categoryCarouselId ?? 'category-carousel-title';
$selectedCategoryId = $categoryId ?? null;
?>
<?php if (!empty($categories)): ?>
    <div class="catalog-category-carousel" aria-labelledby="<?= e($categoryCarouselId) ?>" data-horizontal-carousel>
        <div class="catalog-category-carousel__heading">
            <div><span class="section-kicker"><?= e($categoryCarouselKicker) ?></span><h2 id="<?= e($categoryCarouselId) ?>"><?= e($categoryCarouselTitle) ?></h2></div>
            <div class="carousel-controls"><button type="button" data-carousel-previous aria-label="Ver categorías anteriores">‹</button><button type="button" data-carousel-next aria-label="Ver categorías siguientes">›</button></div>
        </div>
        <div class="category-carousel-viewport" data-carousel-viewport tabindex="0" role="region" aria-label="Categorías disponibles">
            <div class="category-carousel-track">
                <?php foreach ($categories as $category): ?>
                    <?php $selectedCategory = $selectedCategoryId === (int) $category['id_categoria']; ?>
                    <a class="category-carousel-card<?= $selectedCategory ? ' is-active' : '' ?>" href="<?= e(url('products', ['category' => (int) $category['id_categoria']])) ?>" <?= $selectedCategory ? 'aria-current="page"' : '' ?>>
                        <img src="<?= e(category_image($category['imagen'] ?? null)) ?>" alt="" width="220" height="150" loading="lazy" data-image-fallback="<?= e(asset('assets/images/categories/category-placeholder.svg')) ?>">
                        <span><?= e($category['nombre']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
