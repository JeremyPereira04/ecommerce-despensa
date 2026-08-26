<?php
$mobileCurrentPage = (string) ($_GET['page'] ?? 'home');
$mobileCustomer = is_customer_authenticated() ? customer_user() : null;
?>
<nav class="mobile-bottom-nav" aria-label="Navegación móvil">
    <a class="<?= $mobileCurrentPage === 'home' ? 'is-active' : '' ?>" href="<?= e(url('home')) ?>" <?= $mobileCurrentPage === 'home' ? 'aria-current="page"' : '' ?>><i class="bi bi-house" aria-hidden="true"></i><span>Inicio</span></a>
    <a class="<?= $mobileCurrentPage === 'categories' ? 'is-active' : '' ?>" href="<?= e(url('categories')) ?>" <?= $mobileCurrentPage === 'categories' ? 'aria-current="page"' : '' ?>><i class="bi bi-grid" aria-hidden="true"></i><span>Categorías</span></a>
    <a class="<?= $mobileCurrentPage === 'orders' ? 'is-active' : '' ?>" href="<?= e(url($mobileCustomer ? 'orders' : 'login')) ?>" <?= $mobileCurrentPage === 'orders' ? 'aria-current="page"' : '' ?>><i class="bi bi-receipt" aria-hidden="true"></i><span>Pedidos</span></a>
    <a class="mobile-bottom-nav__cart <?= $mobileCurrentPage === 'cart' ? 'is-active' : '' ?>" href="<?= e(url('cart')) ?>" <?= $mobileCurrentPage === 'cart' ? 'aria-current="page"' : '' ?>><i class="bi bi-cart3" aria-hidden="true"></i><span>Carrito</span><?php if ((int) $cartCount > 0): ?><b><?= (int) $cartCount ?></b><?php endif; ?></a>
    <a class="<?= in_array($mobileCurrentPage, ['login', 'register'], true) ? 'is-active' : '' ?>" href="<?= e(url($mobileCustomer ? 'orders' : 'login')) ?>"><i class="bi bi-person" aria-hidden="true"></i><span>Cuenta</span></a>
</nav>
