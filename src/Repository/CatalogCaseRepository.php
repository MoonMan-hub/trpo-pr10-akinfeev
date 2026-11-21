<?php

namespace App\Repository;

use App\Core\CsvFile;

// Сценарии проверки каталога из catalog_cases.csv (case;category;sort;expected_min)
class CatalogCaseRepository
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    // Список сценариев в виде массивов
    public function all(): array
    {
        $cases = [];

        foreach (CsvFile::read($this->file) as $row) {
            if (!isset($row['case'], $row['category'], $row['sort'], $row['expected_min'])) {
                continue;
            }

            $cases[] = [
                'case'         => trim($row['case']),
                'category'     => trim($row['category']),
                'sort'         => trim($row['sort']),
                'expected_min' => (int)$row['expected_min'],
            ];
        }

        return $cases;
    }
}
