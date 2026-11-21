<?php

namespace App\Controller;

use App\Core\View;
use App\Repository\CatalogCaseRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

// Каталог товаров. Данные контроллер получает только от репозиториев.
class CatalogController
{
    private View $view;
    private ProductRepository $products;
    private CategoryRepository $categories;
    private CatalogCaseRepository $cases;

    public function __construct(
        View $view,
        ProductRepository $products,
        CategoryRepository $categories,
        CatalogCaseRepository $cases
    ) {
        $this->view = $view;
        $this->products = $products;
        $this->categories = $categories;
        $this->cases = $cases;
    }

    // GET /catalog?category=1&sort=price
    public function index(): void
    {
        $categoryId = $this->readCategory();
        $sort = $this->readSort();

        $products = $this->products->findFiltered($categoryId, $sort);

        // Если категории с таким id нет, покажем пояснение (список будет пустым)
        $notice = '';
        if ($categoryId !== null && $this->categories->find($categoryId) === null) {
            $notice = 'Категория не найдена.';
        }

        $this->view->render('catalog/index', [
            'title'            => 'Каталог',
            'products'         => $products,
            'categories'       => $this->categories->all(),
            'categoryNames'    => $this->categoryNames(),
            'selectedCategory' => $categoryId,
            'sort'             => $sort,
            'notice'           => $notice,
            'warnings'         => array_merge($this->products->getErrors(), $this->categories->getErrors()),
        ]);
    }

    // GET /product?id=5
    public function show(): void
    {
        $id = false;
        if (isset($_GET['id']) && is_string($_GET['id'])) {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        }

        $product = null;
        if ($id !== false) {
            $product = $this->products->find($id);
        }

        if ($product === null) {
            $this->view->render('catalog/not-found', ['title' => 'Товар не найден'], 404);
            return;
        }

        $category = $this->categories->find($product->getCategoryId());

        $this->view->render('catalog/show', [
            'title'        => $product->getName(),
            'product'      => $product,
            'categoryName' => $category !== null ? $category->getName() : 'Без категории',
        ]);
    }

    // GET /check - прогон сценариев из catalog_cases.csv
    public function check(): void
    {
        $results = [];

        foreach ($this->cases->all() as $case) {
            $categoryId = null;
            if ($case['category'] !== '') {
                $categoryId = (int)$case['category'];
            }

            $count = count($this->products->findFiltered($categoryId, $this->cleanSort($case['sort'])));

            // expected_min больше 0: должно быть не меньше стольких товаров.
            // expected_min = 0: ожидаем пустой список.
            if ($case['expected_min'] > 0) {
                $ok = $count >= $case['expected_min'];
            } else {
                $ok = $count === 0;
            }

            $results[] = [
                'case'     => $case['case'],
                'category' => $case['category'] === '' ? 'все' : $case['category'],
                'sort'     => $case['sort'],
                'expected' => $case['expected_min'],
                'count'    => $count,
                'ok'       => $ok,
            ];
        }

        $this->view->render('catalog/check', [
            'title'   => 'Проверка сценариев',
            'results' => $results,
        ]);
    }

    // ----- разбор параметров запроса (до представления) -----

    // Параметр category: null значит "все категории", 0 значит "неверное значение"
    private function readCategory(): ?int
    {
        if (!isset($_GET['category']) || !is_string($_GET['category']) || $_GET['category'] === '') {
            return null;
        }

        $id = filter_var($_GET['category'], FILTER_VALIDATE_INT);
        if ($id === false) {
            return 0; // категории с id 0 нет, получится пустой список
        }

        return $id;
    }

    // Параметр sort: разрешены только name и price
    private function readSort(): string
    {
        if (isset($_GET['sort']) && is_string($_GET['sort'])) {
            return $this->cleanSort($_GET['sort']);
        }

        return 'name';
    }

    private function cleanSort(string $sort): string
    {
        if ($sort === 'price') {
            return 'price';
        }
        return 'name';
    }

    // Массив [id категории => название] для вывода в карточках
    private function categoryNames(): array
    {
        $names = [];

        foreach ($this->categories->all() as $category) {
            $names[$category->getId()] = $category->getName();
        }

        return $names;
    }
}
