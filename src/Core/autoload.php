<?php

// Простой autoload: класс App\Model\Product лежит в файле src/Model/Product.php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';

    if (strpos($class, $prefix) !== 0) {
        return; // чужие классы не трогаем
    }

    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});
