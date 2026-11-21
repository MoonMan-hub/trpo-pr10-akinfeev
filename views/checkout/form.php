<section class="box">
    <h1>Оформление заказа</h1>

    <h2>Ваш заказ</h2>
    <table class="table">
        <tr>
            <th>Товар</th>
            <th>Количество</th>
            <th>Сумма</th>
        </tr>
        <?php foreach ($lines as $line): ?>
            <tr>
                <td><?= e($line['product']->getName()) ?></td>
                <td><?= e($line['qty']) ?></td>
                <td><?= e(money($line['sum'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <p>Товары: <b><?= e(money($itemsTotal)) ?></b>. Стоимость доставки добавится к итогу после оформления.</p>
</section>

<section class="box" id="checkout-form">
    <h2>Данные для заказа</h2>

    <?php if (count($errors) > 0): ?>
        <div class="error-list">
            <b>Исправьте ошибки:</b>
            <ul>
                <?php foreach ($errors as $message): ?>
                    <li><?= e($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (count($deliveries) === 0): ?>
        <p class="muted">Способы доставки сейчас недоступны.</p>
    <?php else: ?>
        <form method="post" action="/checkout" class="form" novalidate>
            <div class="field">
                <label for="name">Имя</label>
                <input type="text" name="name" id="name" value="<?= e($old['name']) ?>">
                <?php if (isset($errors['name'])): ?>
                    <span class="field-error"><?= e($errors['name']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?= e($old['email']) ?>">
                <?php if (isset($errors['email'])): ?>
                    <span class="field-error"><?= e($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <span class="label">Способ доставки</span>
                <?php foreach ($deliveries as $method): ?>
                    <div class="radio">
                        <input type="radio" name="delivery" id="delivery_<?= e($method->getId()) ?>"
                               value="<?= e($method->getId()) ?>"
                               <?= $old['delivery'] === $method->getId() ? 'checked' : '' ?>>
                        <label for="delivery_<?= e($method->getId()) ?>">
                            <?= e($method->getTitle()) ?> (<?= e($method->getFormattedPrice()) ?>)
                        </label>
                    </div>
                <?php endforeach; ?>
                <?php if (isset($errors['delivery'])): ?>
                    <span class="field-error"><?= e($errors['delivery']) ?></span>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="address">Адрес доставки (нужен для курьера)</label>
                <input type="text" name="address" id="address" value="<?= e($old['address']) ?>">
                <?php if (isset($errors['address'])): ?>
                    <span class="field-error"><?= e($errors['address']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn">Оформить заказ</button>
            <a href="/cart" class="reset">Вернуться в корзину</a>
        </form>
    <?php endif; ?>
</section>
