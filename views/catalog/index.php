<section>
    <h1>Каталог</h1>

    <form method="get" action="/catalog" class="box filter-form">
        <label for="category">Категория:</label>
        <select name="category" id="category">
            <option value="">Все категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category->getId()) ?>"
                    <?= $category->getId() === $selectedCategory ? 'selected' : '' ?>>
                    <?= e($category->getName()) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="sort">Сортировка:</label>
        <select name="sort" id="sort">
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>По названию</option>
            <option value="price" <?= $sort === 'price' ? 'selected' : '' ?>>По цене (сначала дешёвые)</option>
        </select>

        <button type="submit" class="btn">Показать</button>
        <a href="/catalog" class="reset">Сбросить</a>
    </form>

    <?php if (count($warnings) > 0): ?>
        <div class="box warning">
            <b>Некоторые строки данных пропущены:</b>
            <ul>
                <?php foreach ($warnings as $warning): ?>
                    <li><?= e($warning) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($notice !== ''): ?>
        <p class="notice"><?= e($notice) ?></p>
    <?php endif; ?>

    <p class="muted">Найдено товаров: <?= e(count($products)) ?></p>

    <?php if (count($products) === 0): ?>
        <p class="box muted">Товары не найдены.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($products as $product): ?>
                <article class="card">
                    <span class="pill"><?= e($categoryNames[$product->getCategoryId()] ?? 'Без категории') ?></span>
                    <h3><a href="/product?id=<?= e($product->getId()) ?>"><?= e($product->getName()) ?></a></h3>
                    <p class="price"><?= e($product->getFormattedPrice()) ?></p>
                    <?php if ($product->isInStock()): ?>
                        <span class="status ok">в наличии: <?= e($product->getStock()) ?></span>
                    <?php else: ?>
                        <span class="status no">нет в наличии</span>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
