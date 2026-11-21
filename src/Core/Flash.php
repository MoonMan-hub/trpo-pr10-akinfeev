<?php

namespace App\Core;

// Одноразовое сообщение: кладётся в сессию, на следующей странице показывается и удаляется.
class Flash
{
    public static function set(string $type, string $text): void
    {
        $_SESSION['flash'] = ['type' => $type, 'text' => $text];
    }

    // Возвращает сообщение и сразу удаляет его (или null, если сообщения нет)
    public static function pull(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return is_array($flash) ? $flash : null;
    }
}
