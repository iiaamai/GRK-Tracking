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
 * Asset URL with cache-busting version from filemtime.
 */
function asset_v(string $path): string
{
    $url = asset($path);
    $full = APP_ROOT . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'));
    $v = is_file($full) ? (string) filemtime($full) : (string) time();
    $sep = str_contains($url, '?') ? '&' : '?';
    return $url . $sep . 'v=' . rawurlencode($v);
}

/**
 * Public URL for a file stored under the project root (e.g. uploads/bookings/...).
 */
function upload_public_url(?string $relativePath): string
{
    $relativePath = $relativePath === null ? '' : trim(str_replace('\\', '/', $relativePath), '/');
    if ($relativePath === '') {
        return '';
    }

    return rtrim(BASE_URL, '/') . '/' . $relativePath;
}

/**
 * Escape output for HTML.
 */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Format an amount in Philippine Pesos (PHP). Returns an em dash when null.
 */
function format_php_money(?float $amount): string
{
    if ($amount === null) {
        return '—';
    }

    return '₱' . number_format($amount, 2, '.', ',');
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

/**
 * Format a MySQL TIMESTAMP/DATETIME-like string for display.
 * Accepts values like:
 * - `YYYY-MM-DD HH:MM:SS`
 * - `YYYY-MM-DDTHH:MM:SS`
 * - `YYYY-MM-DDTHH:MM`
 */
function format_timestamp(?string $value, ?string $format = null, ?string $timezone = null): string
{
    $value = $value === null ? '' : trim((string) $value);
    if ($value === '') {
        return '';
    }

    // Normalize HTML datetime-local separator (`T`) to MySQL-style space.
    $value = str_replace('T', ' ', $value);

    // datetime-local sometimes omits seconds: `YYYY-MM-DD HH:MM` -> `...:00`
    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value) === 1) {
        $value .= ':00';
    }

    $tzName = $timezone ?: (date_default_timezone_get() ?: 'Asia/Manila');
    $tz = new DateTimeZone($tzName);
    $dt = null;
    try {
        $dt = new DateTimeImmutable($value, $tz);
    } catch (Throwable $e) {
        // If parsing fails, fall back to the raw value (escaped by the caller).
        return $value;
    }

    if ($format === null) {
        // If a value is date-only (e.g. `YYYY-MM-DD`), display it as a date.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            $format = 'M j, Y';
        } else {
            $format = 'M j, Y h:i A';
        }
    }

    return $dt->format($format);
}
