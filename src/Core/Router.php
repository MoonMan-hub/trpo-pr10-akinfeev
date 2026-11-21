<?php

namespace App\Core;

use RuntimeException;

// Маршрутизатор: по методу и пути выбирает контроллер и действие.
// Router не создаёт HTML, он только вызывает метод контроллера.
class Router
{
    private array $routes;
    private array $controllers;

    // $routes - список маршрутов (из routes.csv)
    // $controllers - готовые объекты контроллеров по имени класса
    public function __construct(array $routes, array $controllers)
    {
        $this->routes = $routes;
        $this->controllers = $controllers;
    }

    // Читает routes.csv: method;path;controller;action
    public static function readCsv(string $file): array
    {
        $routes = [];

        foreach (CsvFile::read($file) as $row) {
            if (!isset($row['method'], $row['path'], $row['controller'], $row['action'])) {
                continue;
            }

            $routes[] = [
                'method'     => strtoupper(trim($row['method'])),
                'path'       => trim($row['path']),
                'controller' => trim($row['controller']),
                'action'     => trim($row['action']),
            ];
        }

        return $routes;
    }

    // Из адреса запроса оставляет только путь: /catalog?x=1 -> /catalog, /catalog/ -> /catalog
    public static function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        $path = rtrim($path, '/');
        if ($path === '') {
            return '/';
        }

        return $path;
    }

    // Находит маршрут и вызывает действие контроллера.
    // Если маршрута нет, вызывает ErrorController::notFound
    public function dispatch(string $method, string $path): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['path'] === $path) {
                $this->callAction($route['controller'], $route['action']);
                return;
            }
        }

        $this->callAction('ErrorController', 'notFound');
    }

    private function callAction(string $controllerName, string $action): void
    {
        if (!isset($this->controllers[$controllerName])
            || !method_exists($this->controllers[$controllerName], $action)) {
            throw new RuntimeException('Маршрут настроен неверно: ' . $controllerName . '::' . $action);
        }

        $this->controllers[$controllerName]->$action();
    }
}
