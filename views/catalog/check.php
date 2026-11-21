<section class="box">
    <h1>Проверка сценариев каталога</h1>
    <p><b>Каталог</b> | <a href="/check/orders">Заказ</a></p>
    <p class="muted">Сценарии берутся из data/catalog_cases.csv. Для expected_min = 0 ожидается пустой список.</p>

    <table class="table">
        <tr>
            <th>Сценарий</th>
            <th>Категория</th>
            <th>Сортировка</th>
            <th>Ожидалось (минимум)</th>
            <th>Получено</th>
            <th>Результат</th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?= e($row['case']) ?></td>
                <td><?= e($row['category']) ?></td>
                <td><?= e($row['sort']) ?></td>
                <td><?= e($row['expected']) ?></td>
                <td><?= e($row['count']) ?></td>
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
