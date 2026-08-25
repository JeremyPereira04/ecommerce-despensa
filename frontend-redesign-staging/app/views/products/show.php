<section class="section product-detail-section">
    <div class="container">
        <nav aria-label="Migas de pan"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('home')) ?>">Inicio</a></li><li class="breadcrumb-item"><a href="<?= e(url('products')) ?>">Productos</a></li><li class="breadcrumb-item active" aria-current="page"><?= e($product['nombre'] ?? 'No encontrado') ?></li></ol></nav>
        <?php if ($product === null): ?>
            <?php
            $emptyTitle = 'Producto no encontrado';
            $emptyText = $error ?? 'Puede que el producto ya no esté disponible.';
            $emptyActionUrl = url('products');
            $emptyActionLabel = 'Volver al catálogo';
            require dirname(__DIR__) . '/components/empty-state.php';
            ?>
        <?php else: ?>
            <?php $stock = (int) $product['stock']; ?>
            <div class="row g-4 g-lg-5 align-items-start product-detail">
                <div class="col-lg-6">
                    <div class="product-detail__media">
                        <img src="<?= e(product_image($product['imagen'] ?? null)) ?>" alt="<?= e($product['nombre']) ?>" width="720" height="720" data-image-fallback="<?= e(asset('assets/images/product-placeholder.svg')) ?>">
                        <?php if ($stock < 1): ?><span class="stock-badge stock-badge--out">Sin stock</span><?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product-detail__info">
                        <span class="section-kicker"><?= e($product['categoria_nombre']) ?></span>
                        <h1><?= e($product['nombre']) ?></h1>
                        <p class="product-detail__meta"><?= e($product['marca'] ?: 'Selección local') ?><?php if (!empty($product['presentacion'])): ?> · <?= e($product['presentacion']) ?><?php endif; ?></p>
                        <strong class="product-detail__price"><?= e(money($product['precio'])) ?></strong>
                        <div class="stock-panel <?= $stock < 1 ? 'stock-panel--out' : '' ?>">
                            <span aria-hidden="true"><?= $stock > 0 ? '✓' : '!' ?></span>
                            <div><strong><?= $stock > 0 ? 'Disponible' : 'Producto agotado' ?></strong><small><?= $stock > 0 ? e($stock . ' unidades en stock') : 'No se puede agregar al carrito por ahora.' ?></small></div>
                        </div>
                        <?php if (!empty($product['descripcion'])): ?><p class="product-detail__description"><?= e($product['descripcion']) ?></p><?php endif; ?>
                        <dl class="product-specs">
                            <div><dt>Presentación</dt><dd><?= e($product['presentacion'] ?: 'No especificada') ?></dd></div>
                            <div><dt>Unidad</dt><dd><?= e($product['unidad_medida'] ?: 'No especificada') ?></dd></div>
                            <div><dt>Marca</dt><dd><?= e($product['marca'] ?: 'Sin marca') ?></dd></div>
                        </dl>
                        <form class="add-to-cart" action="<?= e(url('cart-add')) ?>" method="post">
                            <?= csrf_input() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $product['id_producto'] ?>">
                            <div class="quantity-control" data-quantity-control>
                                <label for="product-quantity">Cantidad</label>
                                <div><button type="button" data-quantity-minus aria-label="Restar una unidad">−</button><input id="product-quantity" name="quantity" type="number" min="1" max="<?= max(1, $stock) ?>" value="1" inputmode="numeric"><button type="button" data-quantity-plus aria-label="Sumar una unidad">+</button></div>
                            </div>
                            <button class="btn btn-primary btn-lg flex-grow-1" type="submit" <?= $stock < 1 ? 'disabled' : '' ?>><?= $stock > 0 ? 'Agregar al carrito' : 'Sin stock' ?></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($relatedProducts)): ?>
<section class="section section--soft" aria-labelledby="related-title"><div class="container"><div class="section-heading"><div><span class="section-kicker">También podría interesarte</span><h2 id="related-title">Productos relacionados</h2></div></div><div class="row g-3 g-xl-4"><?php foreach ($relatedProducts as $product): ?><div class="col-12 col-sm-6 col-lg-3"><?php require dirname(__DIR__) . '/components/product-card.php'; ?></div><?php endforeach; ?></div></div></section>
<?php endif; ?>
