<?php

namespace App\Model;

use InvalidArgumentException;

// Модель товара. Хранит данные и умеет отвечать на простые вопросы о себе.
class Product
{
    private int $id;
    private string $name;
    private float $price;
    private int $categoryId; // связь с категорией: равен id из categories.json
    private int $stock;      // дополнительное свойство: остаток на складе

    public function __construct(int $id, string $name, float $price, int $categoryId, int $stock = 0)
    {
        $name = trim($name);

        if ($id <= 0) {
            throw new InvalidArgumentException('id товара должен быть больше 0');
        }
        if ($name === '') {
            throw new InvalidArgumentException('название товара не может быть пустым');
        }
        if ($price < 0) {
            throw new InvalidArgumentException('цена товара не может быть отрицательной');
        }
        if ($categoryId <= 0) {
            throw new InvalidArgumentException('id категории должен быть больше 0');
        }
        if ($stock < 0) {
            throw new InvalidArgumentException('остаток не может быть отрицательным');
        }

        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->categoryId = $categoryId;
        $this->stock = $stock;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    // Есть ли товар на складе
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    // Принадлежит ли товар категории
    public function belongsToCategory(int $categoryId): bool
    {
        return $this->categoryId === $categoryId;
    }

    // Цена для вывода: 570 ₽ или 570,50 ₽
    public function getFormattedPrice(): string
    {
        return money($this->price);
    }
}
