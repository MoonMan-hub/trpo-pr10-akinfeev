<?php

namespace App\Controller;

use App\Core\View;
use App\Repository\OrderCaseRepository;
use App\Service\OrderValidator;

// Проверка валидатора заказа по сценариям из order_cases.json.
class OrderCheckController
{
    private View $view;
    private OrderValidator $validator;
    private OrderCaseRepository $cases;

    public function __construct(View $view, OrderValidator $validator, OrderCaseRepository $cases)
    {
        $this->view = $view;
        $this->validator = $validator;
        $this->cases = $cases;
    }

    // GET /check/orders
    public function check(): void
    {
        $results = [];

        foreach ($this->cases->all() as $case) {
            $input = $this->validator->normalize($case['input']);
            $errors = $this->validator->validate($input);
            $actual = ($errors === []);

            $results[] = [
                'case'     => $case['case'],
                'input'    => $case['input'] === [] ? '(пусто)' : json_encode($case['input'], JSON_UNESCAPED_UNICODE),
                'expected' => $case['valid'],
                'actual'   => $actual,
                'errors'   => $errors,
                'ok'       => $case['valid'] === $actual,
            ];
        }

        $this->view->render('checkout/check', [
            'title'   => 'Проверка сценариев заказа',
            'results' => $results,
        ]);
    }
}
