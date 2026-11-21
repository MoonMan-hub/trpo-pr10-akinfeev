<?php

namespace App\Service;

use App\Repository\ProductRepository;

// Корзина покупателя. В сессии хранится только состояние: [id товара => количество].
// Данные о товаре (название, цена) берутся из ProductRepository.
class CartService
{
    private ProductRepository $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    // Добавляет одну штуку товара. false, если товара нет, он закончился или превышен остаток.
    public function add(int $productId): bool
    {
        $product = $this->products->find($productId);

        if ($product === null || !$product->isInStock()) {
            return false;
        }

        $cart = $this->readCart();
        $quantity = ($cart[$productId] ?? 0) + 1;

        if ($quantity > $product->getStock()) {
            return false;
        }

        $cart[$productId] = $quantity;
        $_SESSION['cart'] = $cart;

        return true;
    }

    // Строки корзины: ['product' => Product, 'qty' => int, 'sum' => float]
    public function lines(): array
    {
        $lines = [];

        foreach ($this->readCart() as $productId => $quantity) {
            $product = $this->products->find((int)$productId);
            if ($product === null) {
                continue; // товар исчез из каталога
            }

            $lines[] = [
                'product' => $product,
                'qty'     => $quantity,
                'sum'     => $product->getPrice() * $quantity,
            ];
        }

        return $lines;
    }

    // Сумма товаров в корзине (без доставки)
    public function total(): float
    {
        $total = 0;

        foreach ($this->lines() as $line) {
            $total += $line['sum'];
        }

        return $total;
    }

    // Общее число штук (для меню)
    public function count(): int
    {
        $count = 0;

        foreach ($this->readCart() as $quantity) {
            $count += $quantity;
        }

        return $count;
    }

    public function isEmpty(): bool
    {
        return count($this->lines()) === 0;
    }

    public function clear(): void
    {
        unset($_SESSION['cart']);
    }

    // Читает корзину из сессии и оставляет только корректные пары [id => количество]
    private function readCart(): array
    {
        $cart = [];
        $stored = $_SESSION['cart'] ?? [];

        if (!is_array($stored)) {
            return [];
        }

        foreach ($stored as $productId => $quantity) {
            if (is_int($quantity) && $quantity > 0) {
                $cart[(int)$productId] = $quantity;
            }
        }

        return $cart;
    }
}
