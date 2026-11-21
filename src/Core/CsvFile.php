<?php

namespace App\Core;

use RuntimeException;

// Чтение CSV-файлов с разделителем ";". Первая строка - заголовок.
class CsvFile
{
    // Возвращает список строк, каждая строка - массив [название_колонки => значение]
    public static function read(string $path): array
    {
        if (!file_exists($path)) {
            throw new RuntimeException('Файл не найден: ' . basename($path));
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false || count($lines) === 0) {
            throw new RuntimeException('Файл пустой или не читается: ' . basename($path));
        }

        // В начале файла может быть невидимый символ BOM, убираем его
        $header = explode(';', trim(str_replace("\xEF\xBB\xBF", '', $lines[0])));
        $rows = [];

        foreach ($lines as $index => $line) {
            if ($index === 0) {
                continue;
            }

            $parts = explode(';', trim($line));
            if (count($parts) < count($header)) {
                continue; // плохая строка пропускается
            }

            $rows[] = array_combine($header, array_slice($parts, 0, count($header)));
        }

        return $rows;
    }
}
