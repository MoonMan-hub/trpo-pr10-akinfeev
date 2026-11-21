<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Страница') ?> | <?= e($storeName) ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="header">
    <a href="/" class="logo">
        <img src="/assets/shop-logo.svg" alt="Логотип магазина" height="40">
        <span><?= e($storeName) ?></span>
    </a>
    <nav class="nav">
        <a href="/" class="<?= $currentPath === '/' ? 'active' : '' ?>">Главная</a>
        <a href="/catalog" class="<?= $currentPath === '/catalog' ? 'active' : '' ?>">Каталог</a>
        <a href="/cart" class="<?= $currentPath === '/cart' ? 'active' : '' ?>">Корзина (<?= e($cartCount) ?>)</a>
        <a href="/about" class="<?= $currentPath === '/about' ? 'active' : '' ?>">О магазине</a>
        <a href="/check" class="<?= $currentPath === '/check' || $currentPath === '/check/orders' ? 'active' : '' ?>">Проверка</a>
    </nav>
</header>

<main class="wrap">
    <?php if ($flash !== null): ?>
        <p class="flash <?= e($flash['type']) ?>"><?= e($flash['text']) ?></p>
    <?php endif; ?>

    <?= $content ?>
</main>

<footer class="footer">
    <p class="muted"><?= e($storeName) ?>. Практическая работа №10, технологии разработки программного обеспечения.</p>
</footer>
</body>
</html>
