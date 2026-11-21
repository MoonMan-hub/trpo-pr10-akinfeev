<?php
// Единая точка входа. Здесь нет разметки: только подключение, создание объектов и запуск Router.

use App\Controller\CartController;
use App\Controller\CatalogController;
use App\Controller\CheckoutController;
use App\Controller\ErrorController;
use App\Controller\HomeController;
use App\Controller\OrderCheckController;
use App\Core\Flash;
use App\Core\Router;
use App\Core\View;
use App\Model\PageModel;
use App\Repository\CatalogCaseRepository;
use App\Repository\CategoryRepository;
use App\Repository\DeliveryMethodRepository;
use App\Repository\OrderCaseRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\OrderValidator;

// Для встроенного сервера PHP: реальные файлы (css, svg) отдаём как есть
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_file($file) && substr($file, -4) !== '.php') {
        return false;
    }
}

// Сессия нужна для корзины и одноразовых сообщений. Вызывается до любого вывода.
session_start();

define('DATA_DIR', __DIR__ . '/../data');

require_once __DIR__ . '/../src/Core/helpers.php';
require_once __DIR__ . '/../src/Core/autoload.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = Router::normalizePath($_SERVER['REQUEST_URI'] ?? '/');

// Общие данные для всех страниц (запасное название на случай ошибки в pages.json)
$view = new View(__DIR__ . '/../views', [
    'storeName'   => 'Интернет-магазин',
    'currentPath' => $path,
    'flash'       => Flash::pull(),
    'cartCount'   => 0,
]);
$errorController = new ErrorController($view);

try {
    $pages = new PageModel(DATA_DIR . '/pages.json');
    $view->share('storeName', $pages->getStoreName());

    // Репозитории получают путь к своему файлу данных
    $productRepository = new ProductRepository(DATA_DIR . '/products.json');
    $categoryRepository = new CategoryRepository(DATA_DIR . '/categories.json');
    $caseRepository = new CatalogCaseRepository(DATA_DIR . '/catalog_cases.csv');
    $deliveryRepository = new DeliveryMethodRepository(DATA_DIR . '/delivery_methods.json');
    $orderRepository = new OrderRepository(DATA_DIR . '/orders.json', DATA_DIR . '/orders_seed.json');
    $orderCaseRepository = new OrderCaseRepository(DATA_DIR . '/order_cases.json');

    // Сервисы
    $cart = new CartService($productRepository);
    $validator = new OrderValidator($deliveryRepository);
    $orderService = new OrderService($orderRepository, $deliveryRepository);

    $view->share('cartCount', $cart->count());

    $controllers = [
        'HomeController'       => new HomeController($view, $pages),
        'CatalogController'    => new CatalogController($view, $productRepository, $categoryRepository, $caseRepository),
        'CartController'       => new CartController($view, $cart),
        'CheckoutController'   => new CheckoutController($view, $cart, $deliveryRepository, $validator, $orderService),
        'OrderCheckController' => new OrderCheckController($view, $validator, $orderCaseRepository),
        'ErrorController'      => $errorController,
    ];

    $router = new Router(Router::readCsv(DATA_DIR . '/routes.csv'), $controllers);
    $router->dispatch($method, $path);
} catch (RuntimeException $e) {
    $errorController->serverError($e->getMessage());
}
