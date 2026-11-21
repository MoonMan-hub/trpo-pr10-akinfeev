<?php

namespace App\Repository;

use App\Core\JsonFile;

// Сценарии проверки формы заказа из order_cases.json
class OrderCaseRepository
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    // Список сценариев: ['case' => ..., 'input' => [...], 'valid' => bool]
    public function all(): array
    {
        $cases = [];

        foreach (JsonFile::read($this->file) as $row) {
            if (!is_array($row) || !isset($row['case'], $row['valid'])) {
                continue;
            }

            $cases[] = [
                'case'  => (string)$row['case'],
                'input' => is_array($row['input'] ?? null) ? $row['input'] : [],
                'valid' => (bool)$row['valid'],
            ];
        }

        return $cases;
    }
}
