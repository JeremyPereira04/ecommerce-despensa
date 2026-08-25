<section class="page-hero page-hero--compact"><div class="container"><nav aria-label="Migas de pan"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('home')) ?>">Inicio</a></li><li class="breadcrumb-item active" aria-current="page">Carrito</li></ol></nav><div class="page-heading"><div><span class="section-kicker">Tu selección</span><h1>Carrito de compras</h1><p>Revisá productos y cantidades antes de continuar.</p></div></div></div></section>
<section class="section cart-section"><div class="container">
    <?php if (!empty($dataError)): ?><div class="alert alert-warning" role="status"><?= e($dataError) ?></div><?php endif; ?>
    <?php if (empty($items)): ?>
        <?php $emptyTitle = 'Tu carrito está vacío'; $emptyText = 'Explorá el catálogo y agregá los productos que necesitás.'; $emptyActionUrl = url('products'); $emptyActionLabel = 'Ir al catálogo'; require dirname(__DIR__) . '/components/empty-state.php'; ?>
    <?php else: ?>
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <form action="<?= e(url('cart-update')) ?>" method="post" id="cart-update-form">
                    <?= csrf_input() ?>
                    <div class="cart-list">
                        <?php foreach ($items as $item): ?>
                            <article class="cart-item">
                                <img src="<?= e(product_image($item['imagen'] ?? null)) ?>" alt="" width="120" height="120" loading="lazy" data-image-fallback="<?= e(asset('assets/images/product-placeholder.svg')) ?>">
                                <div class="cart-item__info"><span><?= e($item['categoria_nombre']) ?></span><h2><a href="<?= e(url('product', ['id' => (int) $item['id_producto']])) ?>"><?= e($item['nombre']) ?></a></h2><small><?= e($item['presentacion'] ?: $item['unidad_medida']) ?></small><strong class="d-lg-none"><?= e(money($item['precio'])) ?></strong></div>
                                <div class="cart-item__price"><span>Precio</span><strong><?= e(money($item['precio'])) ?></strong></div>
                                <div class="quantity-control quantity-control--compact" data-quantity-control><label for="cart-quantity-<?= (int) $item['id_producto'] ?>">Cantidad</label><div><button type="button" data-quantity-minus aria-label="Restar una unidad de <?= e($item['nombre']) ?>">−</button><input id="cart-quantity-<?= (int) $item['id_producto'] ?>" name="items[<?= (int) $item['id_producto'] ?>]" type="number" min="1" max="<?= max(1, (int) $item['stock']) ?>" value="<?= (int) $item['cantidad'] ?>" inputmode="numeric"><button type="button" data-quantity-plus aria-label="Sumar una unidad de <?= e($item['nombre']) ?>">+</button></div></div>
                                <div class="cart-item__subtotal"><span>Subtotal</span><strong><?= e(money($item['subtotal'])) ?></strong></div>
                                <button class="cart-item__remove" type="submit" form="remove-item-<?= (int) $item['id_producto'] ?>" aria-label="Quitar <?= e($item['nombre']) ?> del carrito" data-confirm="¿Querés quitar este producto del carrito?">×</button>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="cart-actions"><a class="btn btn-link" href="<?= e(url('products')) ?>">← Seguir comprando</a><button class="btn btn-outline-primary" type="submit">Actualizar cantidades</button></div>
                </form>
                <?php foreach ($items as $item): ?><form id="remove-item-<?= (int) $item['id_producto'] ?>" action="<?= e(url('cart-remove')) ?>" method="post"><?= csrf_input() ?><input type="hidden" name="product_id" value="<?= (int) $item['id_producto'] ?>"></form><?php endforeach; ?>
            </div>
            <div class="col-lg-4">
                <aside class="order-summary" aria-labelledby="summary-title"><span class="section-kicker">Resumen</span><h2 id="summary-title">Tu pedido</h2><dl><div><dt>Productos</dt><dd><?= count($items) ?></dd></div><div><dt>Envío</dt><dd>A confirmar</dd></div><div class="order-summary__total"><dt>Total estimado</dt><dd><?= e(money($total)) ?></dd></div></dl><p>El stock, envío y total definitivo deben validarse nuevamente en el servidor al confirmar.</p><a class="btn btn-primary btn-lg w-100" href="<?= e(url('checkout')) ?>">Continuar compra</a></aside>
            </div>
        </div>
    <?php endif; ?>
</div></section>
