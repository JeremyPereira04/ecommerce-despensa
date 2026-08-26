<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Migas de pan"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('home')) ?>">Inicio</a></li><li class="breadcrumb-item active" aria-current="page">Productos</li></ol></nav>
        <div class="page-heading"><div><span class="section-kicker">Todo en un lugar</span><h1>Catálogo de productos</h1><p>Explorá el surtido disponible y encontrá lo que necesitás.</p></div><span class="result-count"><?= count($products) ?> resultados</span></div>
    </div>
</section>

<section class="section catalog-section">
    <div class="container">
        <?php if (!empty($error)): ?><div class="alert alert-warning" role="status"><?= e($error) ?></div><?php endif; ?>
        <?php if (!empty($categories)): ?>
            <section class="catalog-category-carousel" aria-labelledby="catalog-categories-title" data-horizontal-carousel>
                <div class="catalog-category-carousel__heading"><div><span class="section-kicker">Encontrá rápido</span><h2 id="catalog-categories-title">Comprá por categoría</h2></div><div class="carousel-controls"><button type="button" data-carousel-previous aria-label="Ver categorías anteriores">‹</button><button type="button" data-carousel-next aria-label="Ver categorías siguientes">›</button></div></div>
                <div class="category-carousel-viewport" data-carousel-viewport tabindex="0">
                    <div class="category-carousel-track">
                        <?php foreach ($categories as $category): ?>
                            <?php $selectedCategory = $categoryId === (int) $category['id_categoria']; ?>
                            <a class="category-carousel-card<?= $selectedCategory ? ' is-active' : '' ?>" href="<?= e(url('products', ['category' => (int) $category['id_categoria']])) ?>" <?= $selectedCategory ? 'aria-current="page"' : '' ?>>
                                <img src="<?= e(category_image($category['imagen'] ?? null)) ?>" alt="" width="220" height="150" loading="lazy" data-image-fallback="<?= e(asset('assets/images/categories/category-placeholder.svg')) ?>">
                                <span><?= e($category['nombre']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <form class="catalog-toolbar" action="<?= e(url('products')) ?>" method="get">
            <input type="hidden" name="page" value="products">
            <div class="filter-search">
                <label for="catalog-search">Buscar</label>
                <input class="form-control" id="catalog-search" type="search" name="q" value="<?= e($search) ?>" placeholder="Nombre, marca o descripción">
            </div>
            <div>
                <label for="category-filter">Categoría</label>
                <select class="form-select" id="category-filter" name="category">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id_categoria'] ?>" <?= $categoryId === (int) $category['id_categoria'] ? 'selected' : '' ?>><?= e($category['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="sort-filter">Ordenar por</label>
                <select class="form-select" id="sort-filter" name="sort">
                    <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nombre</option>
                    <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Menor precio</option>
                    <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Mayor precio</option>
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Aplicar filtros</button>
            <?php if ($search !== '' || $categoryId !== null || $sort !== 'recent'): ?><a class="btn btn-link" href="<?= e(url('products')) ?>">Limpiar</a><?php endif; ?>
        </form>

        <?php if (!empty($products)): ?>
            <div class="row g-3 g-xl-4">
                <?php foreach ($products as $product): ?><div class="col-12 col-sm-6 col-lg-4 col-xl-3"><?php require dirname(__DIR__) . '/components/product-card.php'; ?></div><?php endforeach; ?>
            </div>
            <?php if (count($products) >= 60): ?><p class="pagination-note">Se muestran los primeros 60 productos. La paginación del servidor queda pendiente de integración.</p><?php endif; ?>
        <?php else: ?>
            <?php
            $emptyTitle = $search !== '' ? 'No encontramos coincidencias' : 'No hay productos disponibles';
            $emptyText = $search !== '' ? 'Probá con otro término o quitá los filtros.' : ($error ?? 'Los productos activos aparecerán aquí.');
            $emptyActionUrl = url('products');
            $emptyActionLabel = 'Limpiar búsqueda';
            require dirname(__DIR__) . '/components/empty-state.php';
            ?>
        <?php endif; ?>
    </div>
</section>
