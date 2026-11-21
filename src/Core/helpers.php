<?php

// Экранирование текста перед выводом в HTML
function e($text): string
{
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

// Деньги для вывода: 570 ₽ или 570,50 ₽
function money(float $amount): string
{
    $decimals = ($amount == floor($amount)) ? 0 : 2;
    return number_format($amount, $decimals, ',', ' ') . ' ₽';
}

// Длина строки в символах (работает и с русскими буквами)
function textLength(string $text): int
{
    $length = preg_match_all('/./us', $text);
    return $length === false ? strlen($text) : $length;
}
