<?php

namespace App\Repository;

use App\Core\JsonFile;
use App\Model\Product;
use InvalidArgumentException;

// Источник данных о товарах: читает products.json и возвращает объекты Product.
// HTML здесь нет. Контроллер не знает, что данные лежат в JSON.
class ProductRepository
{
    private string $file;
    private ?array $products = null; // кэш, чтобы не читать файл повторно
    private array $errors = [];      // сообщения о пропущенных строках

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    // Все товары (массив объектов Product)
    public function all(): array
    {
        if ($this->products !== null) {
            return $this->products;
        }

        $this->products = [];
        $number = 0;

        foreach (JsonFile::read($this->file) as $row) {
            $number++;

            if (!is_array($row) || !isset($row['id'], $row['name'], $row['price'], $row['category_id'])) {
                $this->errors[] = 'Товар №' . $number . ' пропущен: не хватает обязательных полей';
                continue;
            }

            try {
                $this->products[] = new Product(
                    (int)$row['id'],
                    (string)$row['name'],
                    (float)$row['price'],
                    (int)$row['category_id'],
                    (int)($row['stock'] ?? 0)
                );
            } catch (InvalidArgumentException $e) {
                $this->errors[] = 'Товар №' . $number . ' пропущен: ' . $e->getMessage();
            }
        }

        return $this->products;
    }

    // Один товар по id или null, если такого нет
    public function find(int $id): ?Product
    {
        foreach ($this->all() as $product) {
            if ($product->getId() === $id) {
                return $product;
            }
        }

        return null;
    }

    // Товары выбранной категории ($categoryId = null значит все) с сортировкой: name или price
    public function findFiltered(?int $categoryId, string $sort): array
    {
        $result = [];

        foreach ($this->all() as $product) {
            if ($categoryId === null || $product->belongsToCategory($categoryId)) {
                $result[] = $product;
            }
        }

        if ($sort === 'price') {
            usort($result, function (Product $a, Product $b) {
                return $a->getPrice() <=> $b->getPrice();
            });
        } else {
            // strnatcmp: "Продукт 2" идёт раньше, чем "Продукт 10"
            usort($result, function (Product $a, Product $b) {
                return strnatcmp($a->getName(), $b->getName());
            });
        }

        return $result;
    }

    // Сообщения о пропущенных строках (заполняются после all())
    public function getErrors(): array
    {
        return $this->errors;
    }
}
