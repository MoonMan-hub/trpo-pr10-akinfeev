<?php

namespace App\Controller;

use App\Core\Redirect;
use App\Core\View;
use App\Model\CheckoutData;
use App\Repository\DeliveryMethodRepository;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\OrderValidator;
use InvalidArgumentException;
use RuntimeException;

// Оформление заказа: GET показывает форму, POST проверяет её и создаёт заказ.
class CheckoutController
{
    private View $view;
    private CartService $cart;
    private DeliveryMethodRepository $deliveries;
    private OrderValidator $validator;
    private OrderService $orders;

    public function __construct(
        View $view,
        CartService $cart,
        DeliveryMethodRepository $deliveries,
        OrderValidator $validator,
        OrderService $orders
    ) {
        $this->view = $view;
        $this->cart = $cart;
        $this->deliveries = $deliveries;
        $this->validator = $validator;
        $this->orders = $orders;
    }

    // GET /checkout - пустая форма
    public function form(): void
    {
        if ($this->cart->isEmpty()) {
            $this->renderEmptyCart();
            return;
        }

        $this->renderForm($this->validator->normalize([]), []);
    }

    // POST /checkout - проверка формы и создание заказа
    public function submit(): void
    {
        if ($this->cart->isEmpty()) {
            $this->renderEmptyCart();
            return;
        }

        // 1. Нормализация и валидация (длинных проверок в контроллере нет)
        $input = $this->validator->normalize($_POST);
        $errors = $this->validator->validate($input);

        // 2. Ошибки: возвращаем ту же форму с введёнными значениями
        if ($errors !== []) {
            $this->renderForm($input, $errors);
            return;
        }

        // 3. DTO создаётся только после успешной проверки
        $data = new CheckoutData($input['name'], $input['email'], $input['delivery'], $input['address']);

        try {
            $order = $this->orders->place($data, $this->cart->lines());
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->renderForm($input, ['form' => 'Не удалось сохранить заказ. Попробуйте позже.']);
            return;
        }

        // 4. Успех: очищаем корзину и делаем redirect (обновление страницы не повторит заказ)
        $this->cart->clear();
        $_SESSION['last_order_id'] = $order->getId();
        Redirect::to('/checkout/success');
    }

    // GET /checkout/success - подтверждение
    public function success(): void
    {
        $orderId = $_SESSION['last_order_id'] ?? 0;
        $order = is_int($orderId) && $orderId > 0 ? $this->orders->find($orderId) : null;

        if ($order === null) {
            $this->view->render('checkout/no-order', ['title' => 'Заказ не найден'], 404);
            return;
        }

        $this->view->render('checkout/success', [
            'title' => 'Заказ оформлен',
            'order' => $order,
        ]);
    }

    private function renderForm(array $old, array $errors): void
    {
        $this->view->render('checkout/form', [
            'title'      => 'Оформление заказа',
            'lines'      => $this->cart->lines(),
            'itemsTotal' => $this->cart->total(),
            'deliveries' => $this->deliveries->all(),
            'old'        => $old,
            'errors'     => $errors,
        ]);
    }

    private function renderEmptyCart(): void
    {
        $this->view->render('checkout/empty', ['title' => 'Корзина пуста']);
    }
}
