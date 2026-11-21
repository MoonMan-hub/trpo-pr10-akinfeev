<?php

namespace App\Model;

// Данные формы оформления заказа (DTO). Создаётся только после успешной валидации.
class CheckoutData
{
    private string $name;
    private string $email;
    private string $delivery; // id способа доставки
    private string $address;

    public function __construct(string $name, string $email, string $delivery, string $address = '')
    {
        $this->name = $name;
        $this->email = $email;
        $this->delivery = $delivery;
        $this->address = $address;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDelivery(): string
    {
        return $this->delivery;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
