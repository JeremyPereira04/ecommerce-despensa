<aside class="admin-sidebar" id="admin-sidebar">
    <a class="admin-brand" href="<?= e(url('admin-dashboard')) ?>"><img src="<?= e(asset($adminLogo)) ?>" width="2022" height="778" alt="Despensa Para Todos"><span>Administración</span></a>
    <nav aria-label="Administración">
        <a class="<?= $adminSection === 'dashboard' ? 'active' : '' ?>" href="<?= e(url('admin-dashboard')) ?>">Dashboard</a>
        <a class="<?= $adminSection === 'products' ? 'active' : '' ?>" href="<?= e(url('admin-products')) ?>">Productos</a>
        <a class="<?= $adminSection === 'categories' ? 'active' : '' ?>" href="<?= e(url('admin-categories')) ?>">Categorías</a>
        <a class="<?= $adminSection === 'orders' ? 'active' : '' ?>" href="<?= e(url('admin-orders')) ?>">Pedidos</a>
        <a class="<?= $adminSection === 'settings' ? 'active' : '' ?>" href="<?= e(url('admin-settings')) ?>">Configuración</a>
    </nav>
    <div class="admin-sidebar__bottom">
        <a href="<?= e(url('home')) ?>">Ver tienda</a>
        <form action="<?= e(url('admin-logout')) ?>" method="post"><?= csrf_input() ?><button type="submit">Cerrar sesión</button></form>
    </div>
</aside>
<button class="admin-overlay" type="button" data-admin-overlay aria-label="Cerrar menú"></button>
