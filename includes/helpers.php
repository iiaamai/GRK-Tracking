<?php
declare(strict_types=1);

/**
 * Redirect helper — use after POST or auth checks.
 */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * Asset URL from web root (BASE_URL).
 */
function asset(string $path): string
{
    return rtrim(BASE_URL, '/') . '/assets/' . ltrim($path, '/');
}

/**
 * Escape output for HTML.
 */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message (one-shot) for form feedback.
 */
function flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    $m = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $m;
}
