<?php

namespace App\Service;

use App\Repository\DeliveryMethodRepository;

// Проверка данных формы заказа. Контроллер сам ничего не проверяет, он вызывает этот класс.
class OrderValidator
{
    private DeliveryMethodRepository $deliveries;

    public function __construct(DeliveryMethodRepository $deliveries)
    {
        $this->deliveries = $deliveries;
    }

    // Приводит данные формы к единому виду: четыре строки без пробелов по краям.
    // Нет поля или оно не строка: подставляется пустая строка.
    public function normalize(array $source): array
    {
        $input = [];

        foreach (['name', 'email', 'delivery', 'address'] as $field) {
            $value = $source[$field] ?? '';
            $input[$field] = is_string($value) ? trim($value) : '';
        }

        return $input;
    }

    // Возвращает массив ошибок [поле => сообщение]. Пустой массив значит "данные верны".
    public function validate(array $input): array
    {
        $errors = [];

        // Имя: обязательное, 2-100 символов
        $name = $input['name'] ?? '';
        if ($name === '') {
            $errors['name'] = 'Введите имя';
        } elseif (textLength($name) < 2 || textLength($name) > 100) {
            $errors['name'] = 'Имя должно быть от 2 до 100 символов';
        }

        // Email: обязательный, корректный формат
        $email = $input['email'] ?? '';
        if ($email === '') {
            $errors['email'] = 'Введите email';
        } elseif (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Проверьте email';
        }

        // Способ доставки: обязателен и должен быть из delivery_methods.json
        $deliveryId = $input['delivery'] ?? '';
        if ($deliveryId === '') {
            $errors['delivery'] = 'Выберите способ доставки';
        } else {
            $delivery = $this->deliveries->find($deliveryId);

            if ($delivery === null) {
                $errors['delivery'] = 'Такого способа доставки нет';
            } elseif ($delivery->requiresAddress()) {
                // Адрес: нужен только для доставки курьером, 5-200 символов
                $address = $input['address'] ?? '';
                if ($address === '') {
                    $errors['address'] = 'Укажите адрес доставки';
                } elseif (textLength($address) < 5 || textLength($address) > 200) {
                    $errors['address'] = 'Адрес должен быть от 5 до 200 символов';
                }
            }
        }

        return $errors;
    }
}
