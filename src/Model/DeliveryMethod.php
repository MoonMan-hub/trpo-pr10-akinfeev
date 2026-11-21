<?php

namespace App\Model;

use InvalidArgumentException;

// Способ доставки (строка из delivery_methods.json)
class DeliveryMethod
{
    private string $id;
    private string $title;
    private float $price;

    public function __construct(string $id, string $title, float $price)
    {
        $id = trim($id);
        $title = trim($title);

        if ($id === '') {
            throw new InvalidArgumentException('id способа доставки не может быть пустым');
        }
        if ($title === '') {
            throw new InvalidArgumentException('название способа доставки не может быть пустым');
        }
        if ($price < 0) {
            throw new InvalidArgumentException('цена доставки не может быть отрицательной');
        }

        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    // Для вывода в форме: "бесплатно" или "450 ₽"
    public function getFormattedPrice(): string
    {
        return $this->price == 0 ? 'бесплатно' : money($this->price);
    }

    // Нужен ли адрес. В dataset такого поля нет, поэтому правило задано по id: адрес нужен курьеру.
    public function requiresAddress(): bool
    {
        return $this->id === 'courier';
    }
}
