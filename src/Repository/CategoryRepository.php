<?php

namespace App\Repository;

use App\Core\JsonFile;
use App\Model\Category;
use InvalidArgumentException;

// Источник данных о категориях: читает categories.json и возвращает объекты Category.
class CategoryRepository
{
    private string $file;
    private ?array $categories = null;
    private array $errors = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    // Все категории (массив объектов Category)
    public function all(): array
    {
        if ($this->categories !== null) {
            return $this->categories;
        }

        $this->categories = [];
        $number = 0;

        foreach (JsonFile::read($this->file) as $row) {
            $number++;

            if (!is_array($row) || !isset($row['id'], $row['name'])) {
                $this->errors[] = 'Категория №' . $number . ' пропущена: не хватает полей id или name';
                continue;
            }

            try {
                $this->categories[] = new Category((int)$row['id'], (string)$row['name']);
            } catch (InvalidArgumentException $e) {
                $this->errors[] = 'Категория №' . $number . ' пропущена: ' . $e->getMessage();
            }
        }

        return $this->categories;
    }

    // Одна категория по id или null
    public function find(int $id): ?Category
    {
        foreach ($this->all() as $category) {
            if ($category->getId() === $id) {
                return $category;
            }
        }

        return null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
