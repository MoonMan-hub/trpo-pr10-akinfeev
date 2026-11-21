<?php

namespace App\Service;

use App\Model\CheckoutData;
use App\Model\Order;
use App\Repository\DeliveryMethodRepository;
use App\Repository\OrderRepository;
use InvalidArgumentException;

// Создание заказа: собирает Order из данных формы и строк корзины и сохраняет его.
class OrderService
{
    private OrderRepository $orders;
    private DeliveryMethodRepository $deliveries;

    public function __construct(OrderRepository $orders, DeliveryMethodRepository $deliveries)
    {
        $this->orders = $orders;
        $this->deliveries = $deliveries;
    }

    // $cartLines - строки из CartService::lines()
    public function place(CheckoutData $data, array $cartLines): Order
    {
        $delivery = $this->deliveries->find($data->getDelivery());
        if ($delivery === null) {
            throw new InvalidArgumentException('Такого способа доставки нет');
        }

        // Состав заказа и сумма товаров
        $items = [];
        $itemsTotal = 0;

        foreach ($cartLines as $line) {
            $product = $line['product'];
            $items[] = [
                'product_id' => $product->getId(),
                'name'       => $product->getName(),
                'price'      => $product->getPrice(),
                'qty'        => $line['qty'],
                'sum'        => $line['sum'],
            ];
            $itemsTotal += $line['sum'];
        }

        // Адрес сохраняем только если способ доставки его требует
        $address = $delivery->requiresAddress() ? $data->getAddress() : '';

        $order = new Order(
            $this->orders->nextId(),
            date('Y-m-d H:i:s'),
            $data->getName(),
            $data->getEmail(),
            $delivery->getId(),
            $delivery->getTitle(),
            $delivery->getPrice(),
            $address,
            $items,
            $itemsTotal
        );

        $this->orders->add($order);

        return $order;
    }

    public function find(int $id): ?Order
    {
        return $this->orders->find($id);
    }
}
