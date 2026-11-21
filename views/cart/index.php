<section class="box">
    <h1>Корзина</h1>

    <?php if (count($lines) === 0): ?>
        <p class="muted">Корзина пуста.</p>
        <p><a href="/catalog" class="btn">Перейти в каталог</a></p>
    <?php else: ?>
        <table class="table">
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Сумма</th>
            </tr>
            <?php foreach ($lines as $line): ?>
                <tr>
                    <td><a href="/product?id=<?= e($line['product']->getId()) ?>"><?= e($line['product']->getName()) ?></a></td>
                    <td><?= e($line['product']->getFormattedPrice()) ?></td>
                    <td><?= e($line['qty']) ?></td>
                    <td><?= e(money($line['sum'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p class="price">Итого: <?= e(money($total)) ?></p>
        <p>
            <a href="/checkout" class="btn">Оформить заказ</a>
            <a href="/catalog" class="reset">Продолжить покупки</a>
        </p>
    <?php endif; ?>
</section>
