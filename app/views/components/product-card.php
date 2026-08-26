<?php
$productId = (int) ($product['id_producto'] ?? 0);
$stock = (int) ($product['stock'] ?? 0);
$productName = (string) ($product['nombre'] ?? 'Producto');
$productCardCompact = $productCardCompact ?? false;
?>
<article class="product-card<?= $productCardCompact ? ' product-card--compact' : '' ?> h-100">
    <a class="product-card__media" href="<?= e(url('product', ['id' => $productId])) ?>" tabindex="-1" aria-hidden="true">
        <img src="<?= e(product_image($product['imagen'] ?? null)) ?>" alt="" width="360" height="360" loading="lazy" data-image-fallback="<?= e(asset('assets/images/product-placeholder.svg')) ?>">
        <?php if ($stock < 1): ?><span class="stock-badge stock-badge--out">Sin stock</span><?php endif; ?>
    </a>
    <div class="product-card__body">
        <p class="product-card__eyebrow"><?= e($product['categoria_nombre'] ?? 'Almacén') ?></p>
        <h3><a href="<?= e(url('product', ['id' => $productId])) ?>"><?= e($productName) ?></a></h3>
        <p class="product-card__meta">
            <?= e($product['marca'] ?: 'Selección local') ?>
            <?php if (!empty($product['presentacion'])): ?> · <?= e($product['presentacion']) ?><?php endif; ?>
        </p>
        <div class="product-card__footer">
            <strong class="price"><?= e(money($product['precio'] ?? 0)) ?></strong>
            <span class="stock-text <?= $stock < 1 ? 'stock-text--out' : '' ?>"><?= $stock > 0 ? e($stock . ' disponibles') : 'Agotado' ?></span>
        </div>
        <div class="product-card__actions">
            <a class="btn btn-outline-primary" href="<?= e(url('product', ['id' => $productId])) ?>">Ver detalle</a>
            <form action="<?= e(url('cart-add')) ?>" method="post">
                <?= csrf_input() ?>
                <input type="hidden" name="product_id" value="<?= $productId ?>">
                <input type="hidden" name="quantity" value="1">
                <button class="btn btn-primary btn-icon" type="submit" <?= $stock < 1 ? 'disabled' : '' ?> aria-label="Agregar <?= e($productName) ?> al carrito">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L20.5 8H6M12 9v5M9.5 11.5h5"/></svg>
                    <?php if ($productCardCompact): ?><span>Añadir</span><?php endif; ?>
                </button>
            </form>
        </div>
    </div>
</article>
