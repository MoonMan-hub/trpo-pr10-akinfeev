<section class="box success-box">
    <h1>Заказ №<?= e($order->getId()) ?> оформлен</h1>
    <p class="muted">Дата: <?= e($order->getCreatedAt()) ?></p>

    <table class="table">
        <tr>
            <th>Товар</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
        </tr>
        <?php foreach ($order->getItems() as $item): ?>
            <tr>
                <td><?= e($item['name'] ?? '') ?></td>
                <td><?= e(money((float)($item['price'] ?? 0))) ?></td>
                <td><?= e($item['qty'] ?? 0) ?></td>
                <td><?= e(money((float)($item['sum'] ?? 0))) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <p>Товары: <?= e(money($order->getItemsTotal())) ?></p>
    <p>Способ доставки: <b><?= e($order->getDeliveryTitle()) ?></b> (<?= e($order->getDeliveryPrice() == 0 ? 'бесплатно' : money($order->getDeliveryPrice())) ?>)</p>
    <p class="price">Итого к оплате: <?= e(money($order->getTotal())) ?></p>

    <p><a href="/catalog" class="btn">Вернуться в каталог</a></p>
</section>
