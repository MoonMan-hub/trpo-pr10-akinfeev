<?php

namespace App\Model;

use InvalidArgumentException;

// Заказ. Поля делятся на три группы: контакт, доставка, состав заказа.
class Order
{
    private int $id;
    private string $createdAt;

    // контакт
    private string $customerName;
    private string $customerEmail;

    // доставка
    private string $deliveryId;
    private string $deliveryTitle;
    private float $deliveryPrice;
    private string $address;

    // состав заказа: список ['product_id', 'name', 'price', 'qty', 'sum']
    private array $items;
    private float $itemsTotal;
    private float $total;

    public function __construct(
        int $id,
        string $createdAt,
        string $customerName,
        string $customerEmail,
        string $deliveryId,
        string $deliveryTitle,
        float $deliveryPrice,
        string $address,
        array $items,
        float $itemsTotal
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('номер заказа должен быть больше 0');
        }
        if (count($items) === 0) {
            throw new InvalidArgumentException('в заказе нет товаров');
        }
        if ($deliveryPrice < 0 || $itemsTotal < 0) {
            throw new InvalidArgumentException('суммы заказа не могут быть отрицательными');
        }

        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->customerName = $customerName;
        $this->customerEmail = $customerEmail;
        $this->deliveryId = $deliveryId;
        $this->deliveryTitle = $deliveryTitle;
        $this->deliveryPrice = $deliveryPrice;
        $this->address = $address;
        $this->items = $items;
        $this->itemsTotal = $itemsTotal;
        $this->total = $itemsTotal + $deliveryPrice; // итог = товары + доставка
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getDeliveryTitle(): string
    {
        return $this->deliveryTitle;
    }

    public function getDeliveryPrice(): float
    {
        return $this->deliveryPrice;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemsTotal(): float
    {
        return $this->itemsTotal;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    // Массив для записи в JSON
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'created_at'  => $this->createdAt,
            'customer'    => ['name' => $this->customerName, 'email' => $this->customerEmail],
            'delivery'    => [
                'id'      => $this->deliveryId,
                'title'   => $this->deliveryTitle,
                'price'   => $this->deliveryPrice,
                'address' => $this->address,
            ],
            'items'       => $this->items,
            'items_total' => $this->itemsTotal,
            'total'       => $this->total,
        ];
    }

    // Создание заказа из строки JSON (обратное к toArray)
    public static function fromArray(array $row): Order
    {
        $customer = is_array($row['customer'] ?? null) ? $row['customer'] : [];
        $delivery = is_array($row['delivery'] ?? null) ? $row['delivery'] : [];

        return new Order(
            (int)($row['id'] ?? 0),
            (string)($row['created_at'] ?? ''),
            (string)($customer['name'] ?? ''),
            (string)($customer['email'] ?? ''),
            (string)($delivery['id'] ?? ''),
            (string)($delivery['title'] ?? ''),
            (float)($delivery['price'] ?? 0),
            (string)($delivery['address'] ?? ''),
            is_array($row['items'] ?? null) ? $row['items'] : [],
            (float)($row['items_total'] ?? 0)
        );
    }
}
