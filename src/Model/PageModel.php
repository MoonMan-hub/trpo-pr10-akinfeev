<?php

namespace App\Model;

use App\Core\JsonFile;
use RuntimeException;

// Модель данных страниц магазина. Читает data/pages.json.
class PageModel
{
    private string $storeName;
    private string $tagline;
    private array $benefits = [];

    // Читает и проверяет файл сразу при создании объекта
    public function __construct(string $file)
    {
        $data = JsonFile::read($file);

        if (!is_array($data) || !isset($data['store_name'], $data['tagline'])) {
            throw new RuntimeException('В файле ' . basename($file) . ' нет полей store_name или tagline');
        }

        $this->storeName = (string)$data['store_name'];
        $this->tagline = (string)$data['tagline'];

        // преимущества необязательны: если их нет, список останется пустым
        if (isset($data['benefits']) && is_array($data['benefits'])) {
            foreach ($data['benefits'] as $benefit) {
                if (is_string($benefit) && trim($benefit) !== '') {
                    $this->benefits[] = trim($benefit);
                }
            }
        }
    }

    public function getStoreName(): string
    {
        return $this->storeName;
    }

    public function getTagline(): string
    {
        return $this->tagline;
    }

    public function getBenefits(): array
    {
        return $this->benefits;
    }
}
