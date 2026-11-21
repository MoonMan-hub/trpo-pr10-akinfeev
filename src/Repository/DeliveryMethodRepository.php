<?php

namespace App\Repository;

use App\Core\JsonFile;
use App\Model\DeliveryMethod;
use InvalidArgumentException;

// Способы доставки из delivery_methods.json
class DeliveryMethodRepository
{
    private string $file;
    private ?array $methods = null;
    private array $errors = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    // Все способы доставки (массив объектов DeliveryMethod)
    public function all(): array
    {
        if ($this->methods !== null) {
            return $this->methods;
        }

        $this->methods = [];
        $number = 0;

        foreach (JsonFile::read($this->file) as $row) {
            $number++;

            if (!is_array($row) || !isset($row['id'], $row['title'], $row['price'])) {
                $this->errors[] = 'Способ доставки №' . $number . ' пропущен: не хватает полей';
                continue;
            }

            try {
                $this->methods[] = new DeliveryMethod((string)$row['id'], (string)$row['title'], (float)$row['price']);
            } catch (InvalidArgumentException $e) {
                $this->errors[] = 'Способ доставки №' . $number . ' пропущен: ' . $e->getMessage();
            }
        }

        return $this->methods;
    }

    // Способ доставки по id или null
    public function find(string $id): ?DeliveryMethod
    {
        foreach ($this->all() as $method) {
            if ($method->getId() === $id) {
                return $method;
            }
        }

        return null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
