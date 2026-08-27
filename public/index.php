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
$timezoneName = (string) ($GLOBALS['appConfig']['timezone'] ?? 'America/Asuncion');
date_default_timezone_set($timezoneName);
if ($isHttps && ($GLOBALS['appConfig']['environment'] ?? 'production') === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

require_once $root . '/app/helpers/view.php';
require_once $root . '/app/helpers/auth.php';
require_once $root . '/app/models/Product.php';
require_once $root . '/app/models/Category.php';
require_once $root . '/app/models/Advertisement.php';
require_once $root . '/app/models/Cart.php';
require_once $root . '/app/models/User.php';
require_once $root . '/app/controllers/ProductController.php';
require_once $root . '/app/controllers/CartController.php';
require_once $root . '/app/controllers/AuthController.php';
require_once $root . '/app/repositories/DashboardRepository.php';
require_once $root . '/app/services/DashboardService.php';
require_once $root . '/app/validators/DashboardValidator.php';
require_once $root . '/app/middlewares/RateLimiter.php';
require_once $root . '/app/middlewares/AdminApiMiddleware.php';
require_once $root . '/app/controllers/DashboardController.php';
require_once $root . '/app/repositories/AdminCatalogRepository.php';
require_once $root . '/app/services/AdminCatalogService.php';
require_once $root . '/app/controllers/AdminCatalogController.php';
require_once $root . '/app/repositories/CheckoutRepository.php';
require_once $root . '/app/controllers/CheckoutController.php';
require_once $root . '/app/repositories/AdminOrderRepository.php';
require_once $root . '/app/controllers/AdminOrderController.php';

$connection = null;
$databaseConfig = $root . '/config/database.php';
if (is_file($databaseConfig)) {
    $databaseOutputLevel = ob_get_level();
    try {
        require_once $databaseConfig;
        $connectionClass = class_exists('AppDatabaseConnection')
            ? 'AppDatabaseConnection'
            : (class_exists('CConexion') ? 'CConexion' : null);
        if ($connectionClass !== null) {
            ob_start();
            $candidate = (new $connectionClass())->conexionBD();
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
$advertisementModel = new Advertisement($connection);
$cartModel = new Cart();
$productController = new ProductController($productModel, $categoryModel, $advertisementModel);
$cartController = new CartController($cartModel, $productModel);
$userModel = new User($connection);
$rateLimiter = new RateLimiter($root . '/storage/rate-limits');
$authController = new AuthController($userModel, $rateLimiter);
$adminCatalogService = $connection instanceof PDO ? new AdminCatalogService(new AdminCatalogRepository($connection)) : null;
$adminCatalogController = $adminCatalogService ? new AdminCatalogController($adminCatalogService, $root . '/public') : null;
$paymentConfigFile=$root.'/config/payment.php';
$paymentConfig=is_file($paymentConfigFile)?require $paymentConfigFile:[];
$checkoutController = $connection instanceof PDO ? new CheckoutController(new CheckoutRepository($connection),$cartModel,$root.'/storage/payment-proofs',$paymentConfig) : null;
$adminOrderController = $connection instanceof PDO ? new AdminOrderController(new AdminOrderRepository($connection),$root.'/storage/payment-proofs') : null;

$requestPath = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$requestPath = '/' . ltrim($requestPath, '/');
$scriptDirectory = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
if ($scriptDirectory !== '/' && $scriptDirectory !== '.' && str_starts_with($requestPath, $scriptDirectory)) {
    $requestPath = substr($requestPath, strlen($scriptDirectory)) ?: '/';
}
$apiRoutes = ['/api/admin/dashboard/stats', '/api/admin/orders/recent'];
if (in_array($requestPath, $apiRoutes, true)) {
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
        header('Allow: GET');
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => ['message' => 'Método no permitido.']], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!$connection instanceof PDO) {
        http_response_code(503);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => ['message' => 'Servicio temporalmente no disponible.']], JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $middleware = new AdminApiMiddleware($connection, $rateLimiter);
        if (!$middleware->handle()) {
            exit;
        }
        $repository = new DashboardRepository($connection);
        $service = new DashboardService(
            $repository,
            new DateTimeZone($timezoneName),
            (int) ($GLOBALS['appConfig']['stock_low_limit'] ?? 5)
        );
        $dashboardController = new DashboardController($service);
        if ($requestPath === '/api/admin/dashboard/stats') {
            $dashboardController->stats();
        } else {
            $dashboardController->recentOrders($_GET['limit'] ?? null);
        }
    } catch (Throwable $exception) {
        error_log('Admin API error: ' . $exception->getMessage());
        (new DashboardController(new DashboardService(
            new DashboardRepository($connection),
            new DateTimeZone($timezoneName)
        )))->error(500, 'Ocurrió un error interno al procesar la solicitud.');
    }
    exit;
}
$adminMockData = require $root . '/app/data/admin-mock.php';
$renderProtectedAdmin = static function (string $view, array $data = []) use ($adminMockData): void {
    require_admin();
    render_admin($view, $data + $adminMockData);
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
    'login' => fn () => $authController->showLogin(),
    'register' => fn () => $authController->showRegister(),
    'orders' => fn () => render('orders/index.php', ['pageTitle' => 'Mis pedidos']),
    'checkout' => fn () => $checkoutController?->show(),
    'order-confirmation' => fn () => $checkoutController?->confirmation(),
    'admin-login' => fn () => $authController->showAdminLogin(),
    'admin-dashboard' => function () use ($renderProtectedAdmin,$connection,$timezoneName):void {
        require_admin();
        $stats=[];
        if($connection instanceof PDO){$stats=(new DashboardService(new DashboardRepository($connection),new DateTimeZone($timezoneName),(int)($GLOBALS['appConfig']['stock_low_limit']??5)))->estadisticas();}
        $renderProtectedAdmin('admin/dashboard.php',['pageTitle'=>'Dashboard','adminSection'=>'dashboard','dashboardStats'=>$stats]);
    },
    'admin-products' => function () use ($renderProtectedAdmin,$adminCatalogService):void { require_admin(); $renderProtectedAdmin('admin/products.php',['pageTitle'=>'Productos','adminSection'=>'products','adminProducts'=>$adminCatalogService?->productos()??[],'adminCategories'=>$adminCatalogService?->categorias()??[]]); },
    'admin-product-create' => function () use ($renderProtectedAdmin,$adminCatalogService):void { require_admin(); $renderProtectedAdmin('admin/product-form.php',['pageTitle'=>'Agregar producto','adminSection'=>'products','editingProduct'=>null,'adminCategories'=>$adminCatalogService?->categorias()??[]]); },
    'admin-product-edit' => function () use ($renderProtectedAdmin,$adminCatalogService):void { require_admin(); $id=filter_var($_GET['id']??null,FILTER_VALIDATE_INT);$product=$id?$adminCatalogService?->producto((int)$id):null;if(!$product){http_response_code(404);render('errors/404.php');return;}$renderProtectedAdmin('admin/product-form.php',['pageTitle'=>'Editar producto','adminSection'=>'products','editingProduct'=>$product,'adminCategories'=>$adminCatalogService?->categorias()??[]]); },
    'admin-categories' => function () use ($renderProtectedAdmin,$adminCatalogService):void { require_admin();$renderProtectedAdmin('admin/categories.php',['pageTitle'=>'Categorías','adminSection'=>'categories','adminCategories'=>$adminCatalogService?->categorias()??[]]); },
    'admin-orders' => fn () => $adminOrderController?->index(),
    'admin-order' => fn () => $adminOrderController?->detail(),
    'admin-payment-proof' => fn () => $adminOrderController?->proof(),
    'admin-settings' => fn () => $renderProtectedAdmin('admin/settings.php', ['pageTitle' => 'Configuración', 'adminSection' => 'settings', 'advertisements' => $adminCatalogService?->publicidades() ?? []]),
];

$postRoutes = [
    'cart-add' => fn () => $cartController->add(),
    'cart-update' => fn () => $cartController->update(),
    'cart-remove' => fn () => $cartController->remove(),
    'admin-login' => fn () => $authController->loginAdmin(),
    'admin-logout' => fn () => $authController->logoutAdmin(),
    'admin-product-save' => fn () => $adminCatalogController?->guardarProducto(null),
    'admin-product-update' => fn () => $adminCatalogController?->guardarProducto((int)($_GET['id']??0)),
    'admin-product-toggle' => fn () => $adminCatalogController?->cambiar('producto',(int)($_GET['id']??0)),
    'admin-category-save' => fn () => $adminCatalogController?->guardarCategoria(null),
    'admin-category-update' => fn () => $adminCatalogController?->guardarCategoria((int)($_GET['id']??0)),
    'admin-category-toggle' => fn () => $adminCatalogController?->cambiar('categoria',(int)($_GET['id']??0)),
    'admin-advertisement-save' => fn () => $adminCatalogController?->guardarPublicidad(),
    'admin-advertisement-toggle' => fn () => $adminCatalogController?->cambiarPublicidad((int)($_GET['id']??0)),
    'admin-advertisement-delete' => fn () => $adminCatalogController?->eliminarPublicidad((int)($_GET['id']??0)),
    'login' => fn () => $authController->loginCustomer(),
    'register' => fn () => $authController->registerCustomer(),
    'logout' => fn () => $authController->logoutCustomer(),
    'checkout-confirm' => fn () => $checkoutController?->confirm(),
    'admin-order-update' => fn () => $adminOrderController?->update(),
    'admin-payment-approve' => fn () => $adminOrderController?->approve(),
];

$routes = $method === 'POST' ? $postRoutes : $getRoutes;
if (!isset($routes[$page])) {
    http_response_code(404);
    render('errors/404.php', ['pageTitle' => 'Página no encontrada']);
    exit;
}

$routes[$page]();
