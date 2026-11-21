<?php

namespace App\Core;

use JsonException;
use RuntimeException;

// Чтение JSON-файлов. Единое место для всех моделей и репозиториев.
class JsonFile
{
    // Возвращает данные файла в виде массива.
    // Если файла нет или JSON сломан, бросает RuntimeException с понятным текстом.
    public static function read(string $path): array
    {
        if (!file_exists($path)) {
            throw new RuntimeException('Файл не найден: ' . basename($path));
        }

        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Не удалось прочитать файл: ' . basename($path));
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException('Файл повреждён (неверный JSON): ' . basename($path));
        }

        if (!is_array($data)) {
            throw new RuntimeException('Неверный формат данных в файле: ' . basename($path));
        }

        return $data;
    }

    // Записывает массив в JSON-файл с защитой от повреждения:
    // сначала пишем во временный файл, потом заменяем им основной.
    // Если запись не удалась, основной файл остаётся нетронутым.
    public static function write(string $path, array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Не удалось преобразовать данные в JSON');
        }

        $tmp = $path . '.tmp';

        if (@file_put_contents($tmp, $json . "\n", LOCK_EX) === false) {
            throw new RuntimeException('Не удалось записать файл: ' . basename($tmp));
        }

        if (!@rename($tmp, $path)) {
            throw new RuntimeException('Не удалось сохранить файл: ' . basename($path));
        }
    }
}
