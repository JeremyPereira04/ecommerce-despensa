<?php

declare(strict_types=1);

final class ProductController
{
    public function __construct(
        private readonly Product $products,
        private readonly Category $categories
    ) {
    }

    public function home(): void
    {
        [$featured, $categories, $error] = $this->loadHomeData();
        render('home.php', [
            'pageTitle' => 'Tu despensa, más cerca',
            'pageDescription' => 'Productos cotidianos, compra simple y atención cercana.',
            'bodyClass' => 'page-home',
            'featuredProducts' => $featured,
            'categories' => $categories,
            'dataError' => $error,
        ]);
    }

    public function index(): void
    {
        $search = trim((string) ($_GET['q'] ?? ''));
        $categoryId = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT) ?: null;
        $sort = (string) ($_GET['sort'] ?? 'recent');

        try {
            $products = $this->products->catalog($search, $categoryId, $sort);
            $categories = $this->categories->all();
            $error = null;
        } catch (Throwable) {
            $products = [];
            $categories = [];
            $error = 'No pudimos cargar el catálogo en este momento.';
        }

        render('products/index.php', compact('products', 'categories', 'search', 'categoryId', 'sort', 'error') + [
            'pageTitle' => 'Catálogo',
            'pageDescription' => 'Explorá los productos disponibles en nuestra despensa.',
        ]);
    }

    public function show(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
        try {
            $product = $this->products->find($id);
            $relatedProducts = $product
                ? $this->products->related((int) $product['id_producto'], (int) $product['id_categoria'])
                : [];
            $error = null;
        } catch (Throwable) {
            $product = null;
            $relatedProducts = [];
            $error = 'No pudimos cargar este producto en este momento.';
        }

        if ($product === null && $error === null) {
            http_response_code(404);
        }

        render('products/show.php', compact('product', 'relatedProducts', 'error') + [
            'pageTitle' => $product['nombre'] ?? 'Producto no encontrado',
        ]);
    }

    private function loadHomeData(): array
    {
        try {
            return [$this->products->featured(), $this->categories->all(), null];
        } catch (Throwable) {
            return [[], [], 'El catálogo no está disponible temporalmente.'];
        }
    }
}
