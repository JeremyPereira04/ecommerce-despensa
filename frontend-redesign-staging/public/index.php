<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$customSessionDirectory = getenv('ECOMMERCE_SESSION_PATH');
$sessionDirectory = is_string($customSessionDirectory) && $customSessionDirectory !== ''
    ? $customSessionDirectory
    : $root . '/storage/sessions';
if (is_dir($sessionDirectory)) {
    session_save_path($sessionDirectory);
}

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => $isHttps,
    'samesite' => 'Lax',
    'path' => '/',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");

$appConfigFile = $root . '/config/app.php';
$GLOBALS['appConfig'] = is_file($appConfigFile) ? require $appConfigFile : [];

require_once $root . '/app/helpers/view.php';
require_once $root . '/app/helpers/auth.php';
require_once $root . '/app/models/Product.php';
require_once $root . '/app/models/Category.php';
require_once $root . '/app/models/Cart.php';
require_once $root . '/app/models/User.php';
require_once $root . '/app/controllers/ProductController.php';
require_once $root . '/app/controllers/CartController.php';
require_once $root . '/app/controllers/AuthController.php';

$connection = null;
$databaseConfig = $root . '/config/database.php';
if (is_file($databaseConfig)) {
    $databaseOutputLevel = ob_get_level();
    try {
        require_once $databaseConfig;
        if (class_exists('CConexion')) {
            ob_start();
            $candidate = (new CConexion())->conexionBD();
            ob_end_clean();
            if ($candidate instanceof PDO) {
                $candidate->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $candidate->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $connection = $candidate;
            }
        }
    } catch (Throwable) {
        while (ob_get_level() > $databaseOutputLevel) {
            ob_end_clean();
        }
        $connection = null;
    }
}

$productModel = new Product($connection);
$categoryModel = new Category($connection);
$cartModel = new Cart();
$productController = new ProductController($productModel, $categoryModel);
$cartController = new CartController($cartModel, $productModel);
$userModel = new User($connection);
$authController = new AuthController($userModel);
$adminMockData = require $root . '/app/data/admin-mock.php';
$renderProtectedAdmin = static function (string $view, array $data = []) use ($adminMockData): void {
    require_admin();
    render_admin($view, $adminMockData + $data);
};

try {
    $GLOBALS['navigationCategories'] = $categoryModel->all();
} catch (Throwable) {
    $GLOBALS['navigationCategories'] = [];
}

$page = preg_replace('/[^a-z-]/', '', strtolower((string) ($_GET['page'] ?? 'home')));
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

$getRoutes = [
    'home' => fn () => $productController->home(),
    'products' => fn () => $productController->index(),
    'product' => fn () => $productController->show(),
    'categories' => function () use ($categoryModel): void {
        try {
            $categories = $categoryModel->all();
            $dataError = null;
        } catch (Throwable) {
            $categories = [];
            $dataError = 'No pudimos cargar las categorías en este momento.';
        }
        render('categories/index.php', compact('categories', 'dataError') + ['pageTitle' => 'Categorías']);
    },
    'cart' => fn () => $cartController->index(),
    'login' => fn () => render('auth/login.php', ['pageTitle' => 'Iniciar sesión']),
    'register' => fn () => render('auth/register.php', ['pageTitle' => 'Crear cuenta']),
    'orders' => fn () => render('orders/index.php', ['pageTitle' => 'Mis pedidos']),
    'checkout' => fn () => render('orders/checkout.php', ['pageTitle' => 'Finalizar compra']),
    'admin-login' => fn () => $authController->showAdminLogin(),
    'admin-dashboard' => fn () => $renderProtectedAdmin('admin/dashboard.php', ['pageTitle' => 'Dashboard', 'adminSection' => 'dashboard']),
    'admin-products' => fn () => $renderProtectedAdmin('admin/products.php', ['pageTitle' => 'Productos', 'adminSection' => 'products']),
    'admin-product-create' => fn () => $renderProtectedAdmin('admin/product-form.php', ['pageTitle' => 'Agregar producto', 'adminSection' => 'products', 'editingProduct' => null]),
    'admin-product-edit' => fn () => $renderProtectedAdmin('admin/product-form.php', ['pageTitle' => 'Editar producto', 'adminSection' => 'products', 'editingProduct' => $adminMockData['adminProducts'][0]]),
    'admin-categories' => fn () => $renderProtectedAdmin('admin/categories.php', ['pageTitle' => 'Categorías', 'adminSection' => 'categories']),
    'admin-orders' => fn () => $renderProtectedAdmin('admin/orders.php', ['pageTitle' => 'Pedidos', 'adminSection' => 'orders']),
    'admin-order' => fn () => $renderProtectedAdmin('admin/order-detail.php', ['pageTitle' => 'Detalle del pedido', 'adminSection' => 'orders']),
    'admin-settings' => fn () => $renderProtectedAdmin('admin/settings.php', ['pageTitle' => 'Configuración', 'adminSection' => 'settings']),
];

$postRoutes = [
    'cart-add' => fn () => $cartController->add(),
    'cart-update' => fn () => $cartController->update(),
    'cart-remove' => fn () => $cartController->remove(),
    'admin-login' => fn () => $authController->loginAdmin(),
    'admin-logout' => fn () => $authController->logoutAdmin(),
];

$routes = $method === 'POST' ? $postRoutes : $getRoutes;
if (!isset($routes[$page])) {
    http_response_code(404);
    render('errors/404.php', ['pageTitle' => 'Página no encontrada']);
    exit;
}

$routes[$page]();
