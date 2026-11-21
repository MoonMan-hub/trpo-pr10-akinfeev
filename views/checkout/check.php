<section class="box">
    <h1>Проверка сценариев заказа</h1>
    <p><a href="/check">Каталог</a> | <b>Заказ</b></p>
    <p class="muted">Сценарии берутся из data/order_cases.json. Каждый прогоняется через тот же OrderValidator, что и форма.</p>

    <table class="table">
        <tr>
            <th>Сценарий</th>
            <th>Входные данные</th>
            <th>Ожидалось</th>
            <th>Получено</th>
            <th>Ошибки валидатора</th>
            <th>Результат</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= e($row['case']) ?></td>
                <td><?= e($row['input']) ?></td>
                <td><?= $row['expected'] ? 'верно' : 'ошибка' ?></td>
                <td><?= $row['actual'] ? 'верно' : 'ошибка' ?></td>
                <td>
                    <?php if (count($row['errors']) === 0): ?>
                        нет
                    <?php else: ?>
                        <?= e(implode('; ', $row['errors'])) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['ok']): ?>
                        <span class="status ok">совпало</span>
                    <?php else: ?>
                        <span class="status no">не совпало</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
