<?php

namespace App\Controller;

use App\Core\Flash;
use App\Core\Redirect;
use App\Core\View;
use App\Service\CartService;

// Корзина: просмотр и добавление товара.
class CartController
{
    private View $view;
    private CartService $cart;

    public function __construct(View $view, CartService $cart)
    {
        $this->view = $view;
        $this->cart = $cart;
    }

    // GET /cart
    public function index(): void
    {
        $this->view->render('cart/index', [
            'title' => 'Корзина',
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
        ]);
    }

    // POST /cart/add (поле product_id)
    public function add(): void
    {
        $productId = false;
        if (isset($_POST['product_id']) && is_string($_POST['product_id'])) {
            $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        }

        if ($productId !== false && $this->cart->add($productId)) {
            Flash::set('ok', 'Товар добавлен в корзину');
        } else {
            Flash::set('error', 'Не удалось добавить товар: его нет, он закончился или превышен остаток');
        }

        Redirect::to('/cart');
    }
}
