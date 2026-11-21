<?php

namespace App\Model;

use InvalidArgumentException;

// Модель категории товаров.
// Связь с товаром: product->categoryId равен category->id.
class Category
{
    private int $id;
    private string $name;

    public function __construct(int $id, string $name)
    {
        $name = trim($name);

        if ($id <= 0) {
            throw new InvalidArgumentException('id категории должен быть больше 0');
        }
        if ($name === '') {
            throw new InvalidArgumentException('название категории не может быть пустым');
        }

        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // Лежит ли товар в этой категории
    public function hasProduct(Product $product): bool
    {
        return $product->belongsToCategory($this->id);
    }
}
