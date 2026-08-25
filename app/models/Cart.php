<?php

declare(strict_types=1);

final class Cart
{
    public function add(int $productId, int $quantity, int $availableStock): void
    {
        if ($productId < 1 || $quantity < 1 || $availableStock < 1) {
            return;
        }

        $current = (int) ($_SESSION['cart'][$productId] ?? 0);
        $_SESSION['cart'][$productId] = min($availableStock, $current + $quantity);
    }

    public function update(array $quantities, array $stockByProduct): void
    {
        foreach ($quantities as $productId => $quantity) {
            $id = filter_var($productId, FILTER_VALIDATE_INT);
            $requested = filter_var($quantity, FILTER_VALIDATE_INT);
            if (!$id || !$requested || $requested < 1 || !isset($stockByProduct[$id])) {
                continue;
            }

            $availableStock = (int) $stockByProduct[$id];
            if ($availableStock < 1) {
                unset($_SESSION['cart'][$id]);
                continue;
            }

            $_SESSION['cart'][$id] = min($availableStock, $requested);
        }
    }

    public function remove(int $productId): void
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function rawItems(): array
    {
        return is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];
    }
}
