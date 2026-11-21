<section class="box">
    <span class="pill"><?= e($categoryName) ?></span>
    <h1><?= e($product->getName()) ?></h1>
    <p class="price"><?= e($product->getFormattedPrice()) ?></p>

    <?php if ($product->isInStock()): ?>
        <p><span class="status ok">в наличии: <?= e($product->getStock()) ?> шт.</span></p>
        <form method="post" action="/cart/add">
            <input type="hidden" name="product_id" value="<?= e($product->getId()) ?>">
            <button type="submit" class="btn">В корзину</button>
        </form>
    <?php else: ?>
        <p><span class="status no">нет в наличии</span></p>
    <?php endif; ?>

    <p class="muted">Код товара: <?= e($product->getId()) ?></p>
    <p>
        <a href="/catalog?category=<?= e($product->getCategoryId()) ?>">Все товары категории «<?= e($categoryName) ?>»</a>
        |
        <a href="/catalog">Весь каталог</a>
    </p>
</section>
