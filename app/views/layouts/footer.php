<?php
$appConfig = $GLOBALS['appConfig'] ?? [];
$contact = $appConfig['contact'] ?? [];
$socialLinks = $appConfig['social'] ?? [];
$phoneDisplay = (string) ($contact['phone_display'] ?? '+595 994 265 663');
$phoneHref = (string) ($contact['phone_href'] ?? 'tel:+595994265663');
$whatsappUrl = (string) ($contact['whatsapp_url'] ?? 'https://wa.me/595994265663');
$businessEmail = (string) ($contact['email'] ?? 'rolonyere@gmail.com');
$businessLocation = (string) ($contact['location'] ?? 'MG37+89G, San Lorenzo 111428');
$mapsUrl = (string) ($contact['maps_url'] ?? 'https://www.google.com/maps/search/?api=1&query=MG37%2B89G%2C%20San%20Lorenzo%20111428');
$socialLabels = [
    'facebook' => 'Visitar Facebook de Despensa Para Todos',
    'instagram' => 'Visitar Instagram de Despensa Para Todos',
    'tiktok' => 'Visitar TikTok de Despensa Para Todos',
    'whatsapp' => 'Contactar a Despensa Para Todos por WhatsApp',
];
$socialIcons = [
    'facebook' => '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v5h4v-5h3l1-4h-4V9c0-.7.3-1 1-1Z"/></svg>',
    'instagram' => '<svg aria-hidden="true" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></svg>',
    'tiktok' => '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 4v10.5a4.5 4.5 0 1 1-4-4.47"/><path d="M14 4c1 3 3 4.5 6 4.5"/></svg>',
    'whatsapp' => '<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11.7a8 8 0 0 1-11.8 7L4 20l1.3-4.1A8 8 0 1 1 20 11.7Z"/><path d="M8.5 8.2c.4 3.3 2.1 5 5.3 6.2l1.3-1.4 2 .9c.2 1-.5 2.1-1.5 2.4-3.5.4-8.4-4.1-8.2-7.4.1-.8.5-1.2 1.1-.7Z"/></svg>',
];
?>

<section class="service-benefits" aria-labelledby="service-benefits-title">
    <div class="footer-container">
        <h2 class="visually-hidden" id="service-benefits-title">Beneficios de comprar con nosotros</h2>
        <div class="service-benefits__grid">
            <article class="service-benefit">
                <span class="service-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="6" y="10" width="12" height="10" rx="2"/><path d="M9 10V7a3 3 0 0 1 6 0v3M9.5 15l1.5 1.5 3.5-3.5"/></svg></span>
                <div><h3>Compra segura</h3><p>Protegemos tus datos y verificamos cada pedido.</p></div>
            </article>
            <article class="service-benefit">
                <span class="service-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 7h16l-1 13H5L4 7Z"/><path d="M8 7a4 4 0 0 1 8 0M9 13l2 2 4-4"/></svg></span>
                <div><h3>Stock actualizado</h3><p>Productos disponibles según el inventario registrado.</p></div>
            </article>
            <article class="service-benefit">
                <span class="service-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M5 13v-2a7 7 0 0 1 14 0v2"/><path d="M5 13H3v5h3v-5ZM19 13h2v5h-3M18 18c0 2-2 3-5 3"/></svg></span>
                <div><h3>Atención cercana</h3><p>Comunicate directamente con nuestro local.</p></div>
            </article>
            <article class="service-benefit">
                <span class="service-benefit__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M4 8h16v12H4V8Z"/><path d="M8 8V5h8v3M12 12v4M10 14h4"/></svg></span>
                <div><h3>Retiro rápido</h3><p>Prepará tu pedido y retiralo sin demoras.</p></div>
            </article>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-container footer-grid">
            <section class="footer-brand-block" aria-label="Despensa Para Todos">
                <a class="brand brand--footer" href="<?= e(url('home')) ?>" aria-label="Despensa Para Todos, ir al inicio">
                    <?php if ($brandLogoExists): ?>
                        <img class="brand__logo brand__logo--footer" src="<?= e(asset($brandLogoPath)) ?>" alt="Logo de Despensa Para Todos" width="2022" height="778" loading="lazy">
                    <?php else: ?>
                        <span class="brand__fallback"><strong>Despensa</strong><small>Para Todos</small></span>
                    <?php endif; ?>
                </a>
                <p class="footer-intro">Productos para todos los días, con atención cercana y una compra simple.</p>
                <nav class="footer-social" aria-label="Redes sociales">
                    <h2 class="footer-title">Seguinos</h2>
                    <div class="footer-social__links">
                        <?php foreach ($socialLinks as $network => $socialUrl): ?>
                            <?php if (!is_string($socialUrl) || trim($socialUrl) === '' || !isset($socialIcons[$network], $socialLabels[$network])) { continue; } ?>
                            <a class="social-link" href="<?= e($socialUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= e($socialLabels[$network]) ?>"><?= $socialIcons[$network] ?></a>
                        <?php endforeach; ?>
                    </div>
                </nav>
            </section>

            <nav class="footer-column" aria-label="Enlaces para comprar">
                <h2 class="footer-title">Comprar</h2>
                <ul class="footer-links">
                    <li><a href="<?= e(url('products')) ?>">Todos los productos</a></li>
                    <li><a href="<?= e(url('categories')) ?>">Categorías</a></li>
                    <li><a href="<?= e(url('cart')) ?>">Mi carrito</a></li>
                    <li><a href="<?= e(url('orders')) ?>">Mis pedidos</a></li>
                </ul>
            </nav>

            <section class="footer-column" aria-labelledby="footer-help-title">
                <h2 class="footer-title" id="footer-help-title">Ayuda</h2>
                <ul class="footer-links footer-links--pending">
                    <li><span data-route-status="pending">Preguntas frecuentes</span></li>
                    <li><span data-route-status="pending">Cómo comprar</span></li>
                    <li><span data-route-status="pending">Medios de pago</span></li>
                    <li><span data-route-status="pending">Contacto</span></li>
                    <li><span data-route-status="pending">Términos y condiciones</span></li>
                </ul>
            </section>

            <nav class="footer-column footer-contact" aria-label="Información de contacto">
                <h2 class="footer-title">Contacto</h2>
                <ul class="footer-links footer-links--contact">
                    <li><a class="contact-link contact-link--nowrap" href="<?= e($phoneHref) ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M6.6 3h3l1.2 5-2 1.3a15 15 0 0 0 5.9 5.9l1.3-2 5 1.2v3c0 2-1.6 3.6-3.6 3.5A15.8 15.8 0 0 1 3.1 6.6C3 4.6 4.6 3 6.6 3Z"/></svg><span><?= e($phoneDisplay) ?></span></a></li>
                    <li><a class="contact-link contact-link--nowrap" href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11.7a8 8 0 0 1-11.8 7L4 20l1.3-4.1A8 8 0 1 1 20 11.7Z"/><path d="M8.5 8.2c.4 3.3 2.1 5 5.3 6.2"/></svg><span><?= e($phoneDisplay) ?></span></a></li>
                    <li><a class="contact-link contact-link--nowrap contact-link--email" href="mailto:<?= e($businessEmail) ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg><span><?= e($businessEmail) ?></span></a></li>
                    <li><a class="contact-link contact-link--location" href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener noreferrer"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg><span><?= e($businessLocation) ?></span></a></li>
                </ul>
            </nav>

            <section class="footer-column" aria-labelledby="footer-hours-title">
                <h2 class="footer-title" id="footer-hours-title">Horarios</h2>
                <ul class="footer-hours">
                    <li><strong>Lunes a viernes</strong><span>07:00 a 00:00</span></li>
                    <li><strong>Sábado</strong><span>12:00 a 19:00</span></li>
                    <li><strong>Domingo</strong><span>Cerrado</span></li>
                </ul>
            </section>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-container"><small>&copy; 2026 Despensa Para Todos. Todos los derechos reservados.</small></div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>
<script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</body>
</html>
