<?php

namespace App\Repository;

use App\Core\JsonFile;
use App\Model\Order;
use InvalidArgumentException;

// Учебное хранилище заказов в JSON.
// Заказы пишутся в $file (data/orders.json). Пока этого файла нет, исходным списком
// служит $seedFile (data/orders_seed.json).
class OrderRepository
{
    private string $file;
    private string $seedFile;

    public function __construct(string $file, string $seedFile)
    {
        $this->file = $file;
        $this->seedFile = $seedFile;
    }

    // Сырые строки файла (массивы). Если файла заказов ещё нет, берём seed.
    private function readRows(): array
    {
        if (file_exists($this->file)) {
            return JsonFile::read($this->file);
        }

        return JsonFile::read($this->seedFile);
    }

    // Следующий номер заказа: максимальный существующий + 1
    public function nextId(): int
    {
        $maxId = 0;

        foreach ($this->readRows() as $row) {
            if (is_array($row) && isset($row['id']) && (int)$row['id'] > $maxId) {
                $maxId = (int)$row['id'];
            }
        }

        return $maxId + 1;
    }

    // Добавляет заказ. Старые строки сохраняются как есть.
    // Если файл заказов повреждён, readRows() бросит исключение, и файл не будет перезаписан.
    public function add(Order $order): void
    {
        $rows = $this->readRows();
        $rows[] = $order->toArray();

        JsonFile::write($this->file, $rows);
    }

    // Заказ по номеру или null
    public function find(int $id): ?Order
    {
        foreach ($this->readRows() as $row) {
            if (!is_array($row) || (int)($row['id'] ?? 0) !== $id) {
                continue;
            }

            try {
                return Order::fromArray($row);
            } catch (InvalidArgumentException $e) {
                return null; // строка повреждена
            }
        }

        return null;
    }
}
