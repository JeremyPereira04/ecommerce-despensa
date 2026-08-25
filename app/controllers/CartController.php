<?php

declare(strict_types=1);

final class CartController
{
    public function __construct(
        private readonly Cart $cart,
        private readonly Product $products
    ) {
    }

    public function index(): void
    {
        $items = [];
        $total = 0.0;
        $dataError = null;

        try {
            foreach ($this->cart->rawItems() as $productId => $quantity) {
                $product = $this->products->find((int) $productId);
                if ($product === null) {
                    continue;
                }
                $product['cantidad'] = min((int) $quantity, (int) $product['stock']);
                $product['subtotal'] = (float) $product['precio'] * $product['cantidad'];
                $total += $product['subtotal'];
                $items[] = $product;
            }
        } catch (Throwable) {
            $dataError = 'No pudimos actualizar los datos del carrito.';
        }

        render('cart/index.php', compact('items', 'total', 'dataError') + ['pageTitle' => 'Tu carrito']);
    }

    public function add(): never
    {
        $this->requireValidToken();
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: 0;
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 0;

        try {
            $product = $this->products->find($id);
            if ($product === null || (int) $product['stock'] < 1 || $quantity < 1) {
                flash('warning', 'El producto o la cantidad seleccionada no están disponibles.');
            } else {
                $this->cart->add($id, $quantity, (int) $product['stock']);
                flash('success', 'Agregamos el producto a tu carrito.');
            }
        } catch (Throwable) {
            flash('danger', 'No pudimos agregar el producto. Intentá nuevamente.');
        }

        $this->redirect('cart');
    }

    public function update(): never
    {
        $this->requireValidToken();
        $quantities = is_array($_POST['items'] ?? null) ? $_POST['items'] : [];
        $stock = [];

        try {
            foreach (array_keys($quantities) as $productId) {
                $id = filter_var($productId, FILTER_VALIDATE_INT);
                $product = $id ? $this->products->find($id) : null;
                if ($product !== null) {
                    $stock[$id] = (int) $product['stock'];
                }
            }
            $this->cart->update($quantities, $stock);
            flash('success', 'Actualizamos las cantidades del carrito.');
        } catch (Throwable) {
            flash('danger', 'No pudimos actualizar el carrito.');
        }

        $this->redirect('cart');
    }

    public function remove(): never
    {
        $this->requireValidToken();
        $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT) ?: 0;
        if ($id > 0) {
            $this->cart->remove($id);
            flash('info', 'Quitamos el producto del carrito.');
        }
        $this->redirect('cart');
    }

    private function requireValidToken(): void
    {
        if (!verify_csrf($_POST['csrf_token'] ?? null)) {
            http_response_code(403);
            flash('danger', 'La sesión del formulario venció. Volvé a intentarlo.');
            $this->redirect('cart');
        }
    }

    private function redirect(string $page): never
    {
        header('Location: ' . url($page));
        exit;
    }
}
